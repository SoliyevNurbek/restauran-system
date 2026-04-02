<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $lowStockProducts = $this->lowStockProducts();

        $stats = [
            'todayPurchases' => (float) Purchase::whereDate('purchase_date', $today)->sum('total_amount'),
            'monthPurchases' => (float) Purchase::whereBetween('purchase_date', [$monthStart->toDateString(), now()->toDateString()])->sum('total_amount'),
            'monthExpenses' => (float) Expense::whereBetween('expense_date', [$monthStart->toDateString(), now()->toDateString()])->sum('amount'),
            'lowStockCount' => $lowStockProducts->count(),
            'productCount' => Product::count(),
            'supplierCount' => Supplier::count(),
        ];

        $purchaseRows = Purchase::query()
            ->selectRaw('DATE(purchase_date) as date_value, SUM(total_amount) as amount')
            ->whereDate('purchase_date', '>=', $today->copy()->subDays(6))
            ->groupBy(DB::raw('DATE(purchase_date)'))
            ->orderBy('date_value')
            ->get()
            ->keyBy('date_value');

        $expenseRows = Expense::query()
            ->selectRaw('DATE(expense_date) as date_value, SUM(amount) as amount')
            ->whereDate('expense_date', '>=', $today->copy()->subDays(6))
            ->groupBy(DB::raw('DATE(expense_date)'))
            ->orderBy('date_value')
            ->get()
            ->keyBy('date_value');

        $labels = [];
        $purchaseValues = [];
        $expenseValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $date = $day->toDateString();
            $labels[] = $day->format('d M');
            $purchaseValues[] = isset($purchaseRows[$date]) ? (float) $purchaseRows[$date]->amount : 0;
            $expenseValues[] = isset($expenseRows[$date]) ? (float) $expenseRows[$date]->amount : 0;
        }

        $supplierDebt = Supplier::query()
            ->withSum('purchases', 'total_amount')
            ->withSum('payments', 'amount')
            ->get()
            ->sum->balance;

        return view('dashboard.index', [
            'stats' => $stats + ['supplierDebt' => $supplierDebt],
            'chartLabels' => $labels,
            'purchaseValues' => $purchaseValues,
            'expenseValues' => $expenseValues,
            'latestPurchases' => Purchase::with(['supplier', 'items.product'])->latest('purchase_date')->take(6)->get(),
            'latestExpenses' => Expense::with('category')->latest('expense_date')->take(6)->get(),
            'lowStockProducts' => $lowStockProducts->take(6),
            'lowStockProductsFull' => $lowStockProducts,
            'topSuppliers' => Supplier::query()
                ->withSum('purchases', 'total_amount')
                ->withSum('payments', 'amount')
                ->orderByDesc('purchases_sum_total_amount')
                ->take(6)
                ->get(),
        ]);
    }

    public function exportLowStockWord(): BinaryFileResponse
    {
        $generatedAt = now();
        $products = $this->lowStockProducts();
        $directory = storage_path('app/private/exports');

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filePath = $directory.'/kam-qolgan-mahsulotlar-'.$generatedAt->format('Y-m-d-H-i-s').'.docx';

        $this->createLowStockDocx($filePath, $products, $generatedAt);

        return response()->download(
            $filePath,
            'kam-qolgan-mahsulotlar-'.$generatedAt->format('Y-m-d-H-i').'.docx',
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'X-Content-Type-Options' => 'nosniff',
            ]
        )->deleteFileAfterSend(true);
    }

    private function lowStockProducts()
    {
        return Product::query()
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderByRaw('(minimum_stock - current_stock) DESC')
            ->orderBy('current_stock')
            ->get()
            ->map(function (Product $product) {
                $product->restock_amount = max((float) $product->minimum_stock - (float) $product->current_stock, 0);

                return $product;
            });
    }

    private function createLowStockDocx(string $filePath, $products, Carbon $generatedAt): void
    {
        $zip = new ZipArchive();

        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'DOCX fayl yaratib bo\'lmadi.');
        }

        $zip->addFromString('[Content_Types].xml', $this->docxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->docxRootRelsXml());
        $zip->addFromString('word/_rels/document.xml.rels', $this->docxDocumentRelsXml());
        $zip->addFromString('word/styles.xml', $this->docxStylesXml());
        $zip->addFromString('docProps/core.xml', $this->docxCoreXml($generatedAt));
        $zip->addFromString('docProps/app.xml', $this->docxAppXml());
        $zip->addFromString('word/document.xml', $this->docxDocumentXml($products, $generatedAt));
        $zip->close();
    }

    private function docxContentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
    <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML;
    }

    private function docxRootRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    }

    private function docxDocumentRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    private function docxStylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:style w:type="paragraph" w:default="1" w:styleId="Normal">
        <w:name w:val="Normal"/>
        <w:qFormat/>
        <w:rPr>
            <w:sz w:val="22"/>
        </w:rPr>
    </w:style>
    <w:style w:type="paragraph" w:styleId="Title">
        <w:name w:val="Title"/>
        <w:basedOn w:val="Normal"/>
        <w:qFormat/>
        <w:rPr>
            <w:b/>
            <w:sz w:val="32"/>
        </w:rPr>
    </w:style>
</w:styles>
XML;
    }

    private function docxCoreXml(Carbon $generatedAt): string
    {
        $timestamp = $generatedAt->copy()->utc()->format('Y-m-d\TH:i:s\Z');

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:title>Kam qolgan mahsulotlar</dc:title>
    <dc:creator>Restoran CRM</dc:creator>
    <cp:lastModifiedBy>Restoran CRM</cp:lastModifiedBy>
    <dcterms:created xsi:type="dcterms:W3CDTF">{$timestamp}</dcterms:created>
    <dcterms:modified xsi:type="dcterms:W3CDTF">{$timestamp}</dcterms:modified>
</cp:coreProperties>
XML;
    }

    private function docxAppXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>Restoran CRM</Application>
</Properties>
XML;
    }

    private function docxDocumentXml($products, Carbon $generatedAt): string
    {
        $rows = '';

        $rows .= $this->docxTableRow([
            ['#', true],
            ['Mahsulot', true],
            ['Qancha qoldi', true],
            ['Qancha olib kelish kerak', true],
        ]);

        foreach ($products as $index => $product) {
            $rows .= $this->docxTableRow([
                [(string) ($index + 1), false],
                [$product->name, false],
                [number_format($product->current_stock, 3).' '.$product->unit, false],
                [number_format($product->restock_amount, 3).' '.$product->unit, false],
            ]);
        }

        if ($products->isEmpty()) {
            $rows .= $this->docxTableRow([
                ['Kam qolgan mahsulotlar topilmadi.', false],
                ['', false],
                ['', false],
                ['', false],
            ]);
        }

        $generatedText = $this->docxEscape('Yaratilgan vaqt: '.$generatedAt->format('d.m.Y H:i'));
        $formulaText = $this->docxEscape('Hisoblash formulasi: minimal qoldiq - joriy qoldiq.');

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" xmlns:w15="http://schemas.microsoft.com/office/word/2012/wordml" mc:Ignorable="w14 w15 wp14">
    <w:body>
        <w:p>
            <w:pPr><w:pStyle w:val="Title"/></w:pPr>
            <w:r><w:t>Kam qolgan mahsulotlar ro'yxati</w:t></w:r>
        </w:p>
        <w:p><w:r><w:t>{$generatedText}</w:t></w:r></w:p>
        <w:tbl>
            <w:tblPr>
                <w:tblBorders>
                    <w:top w:val="single" w:sz="8" w:space="0" w:color="AAB4C3"/>
                    <w:left w:val="single" w:sz="8" w:space="0" w:color="AAB4C3"/>
                    <w:bottom w:val="single" w:sz="8" w:space="0" w:color="AAB4C3"/>
                    <w:right w:val="single" w:sz="8" w:space="0" w:color="AAB4C3"/>
                    <w:insideH w:val="single" w:sz="8" w:space="0" w:color="D7DEE8"/>
                    <w:insideV w:val="single" w:sz="8" w:space="0" w:color="D7DEE8"/>
                </w:tblBorders>
            </w:tblPr>
            {$rows}
        </w:tbl>
        <w:p><w:r><w:t>{$formulaText}</w:t></w:r></w:p>
        <w:sectPr>
            <w:pgSz w:w="11906" w:h="16838"/>
            <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="708" w:footer="708" w:gutter="0"/>
        </w:sectPr>
    </w:body>
</w:document>
XML;
    }

    private function docxTableRow(array $cells): string
    {
        $xml = '<w:tr>';

        foreach ($cells as [$text, $bold]) {
            $escapedText = $this->docxEscape($text);
            $runProps = $bold ? '<w:rPr><w:b/></w:rPr>' : '';
            $cellProps = $bold ? '<w:shd w:val="clear" w:color="auto" w:fill="E2E8F0"/>' : '';

            $xml .= <<<XML
<w:tc>
    <w:tcPr>{$cellProps}</w:tcPr>
    <w:p>
        <w:r>{$runProps}<w:t xml:space="preserve">{$escapedText}</w:t></w:r>
    </w:p>
</w:tc>
XML;
        }

        $xml .= '</w:tr>';

        return $xml;
    }

    private function docxEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
