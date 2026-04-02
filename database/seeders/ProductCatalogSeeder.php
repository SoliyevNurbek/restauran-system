<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $csv = <<<'CSV'
OSH-GOSHT-001,Mol go'shti,oshxona,go'sht,kg
OSH-GOSHT-002,Qo'y go'shti,oshxona,go'sht,kg
OSH-GOSHT-003,Tovuq go'shti,oshxona,go'sht,kg
OSH-GOSHT-004,Qiyma,oshxona,go'sht,kg
OSH-GOSHT-005,Suyakli go'sht,oshxona,go'sht,kg
OSH-GURUCH-001,Guruch lazer,oshxona,yorma,kg
OSH-GURUCH-002,Guruch alanga,oshxona,yorma,kg
OSH-YORMA-001,Mosh,oshxona,yorma,kg
OSH-YORMA-002,No'xat,oshxona,yorma,kg
OSH-YORMA-003,Loviya,oshxona,yorma,kg
OSH-YORMA-004,Grechka,oshxona,yorma,kg
OSH-YORMA-005,Makaroni,oshxona,yorma,kg
OSH-YORMA-006,Spagetti,oshxona,yorma,kg
OSH-YORMA-007,Un,oshxona,yorma,kg
OSH-YORMA-008,Kraxmal,oshxona,yorma,kg
OSH-SABZ-001,Piyoz,oshxona,sabzavot,kg
OSH-SABZ-002,Sabzi,oshxona,sabzavot,kg
OSH-SABZ-003,Kartoshka,oshxona,sabzavot,kg
OSH-SABZ-004,Pomidor,oshxona,sabzavot,kg
OSH-SABZ-005,Bodring,oshxona,sabzavot,kg
OSH-SABZ-006,Bolg'or qalampiri,oshxona,sabzavot,kg
OSH-SABZ-007,Achchiq qalampir,oshxona,sabzavot,kg
OSH-SABZ-008,Sarimsoq,oshxona,sabzavot,kg
OSH-SABZ-009,Karam,oshxona,sabzavot,kg
OSH-SABZ-010,Gulkaram,oshxona,sabzavot,kg
OSH-SABZ-011,Ko'kat aralash,oshxona,sabzavot,bog'
OSH-SABZ-012,Shivit,oshxona,sabzavot,bog'
OSH-SABZ-013,Kashnich,oshxona,sabzavot,bog'
OSH-SABZ-014,Rayhon,oshxona,sabzavot,bog'
OSH-SABZ-015,Salat bargi,oshxona,sabzavot,dona
OSH-SABZ-016,Rediska,oshxona,sabzavot,kg
OSH-SABZ-017,Limon,oshxona,meva,kg
OSH-SABZ-018,Olma,oshxona,meva,kg
OSH-SABZ-019,Uzum,oshxona,meva,kg
OSH-SABZ-020,Anor,oshxona,meva,kg
OSH-SABZ-021,Apelsin,oshxona,meva,kg
OSH-SABZ-022,Banan,oshxona,meva,kg
OSH-YOG-001,Paxta yog'i,oshxona,yog',litr
OSH-YOG-002,Kungaboqar yog'i,oshxona,yog',litr
OSH-YOG-003,Sariyog',oshxona,yog',kg
OSH-YOG-004,Margarin,oshxona,yog',kg
OSH-ZIR-001,Tuz,oshxona,ziravor,kg
OSH-ZIR-002,Qora murch,oshxona,ziravor,kg
OSH-ZIR-003,Qizil murch,oshxona,ziravor,kg
OSH-ZIR-004,Zira,oshxona,ziravor,kg
OSH-ZIR-005,Kashnich urug'i,oshxona,ziravor,kg
OSH-ZIR-006,Dafna yaprog'i,oshxona,ziravor,paket
OSH-ZIR-007,Kurkuma,oshxona,ziravor,kg
OSH-ZIR-008,Paprika,oshxona,ziravor,kg
OSH-ZIR-009,Mayonez,oshxona,sous,kg
OSH-ZIR-010,Ketchup,oshxona,sous,kg
OSH-ZIR-011,Xantal,oshxona,sous,kg
OSH-ZIR-012,Sirka,oshxona,sous,litr
OSH-ZIR-013,Soya sousi,oshxona,sous,litr
OSH-ZIR-014,Tomat pastasi,oshxona,sous,kg
SAL-MAS-001,Kolbasa,oshxona,salat,kg
SAL-MAS-002,Sosiska,oshxona,salat,kg
SAL-MAS-003,Qattiq pishloq,oshxona,salat,kg
SAL-MAS-004,Suyuq pishloq,oshxona,salat,kg
SAL-MAS-005,Tuxum,oshxona,salat,dona
SAL-MAS-006,Zaytun qora,oshxona,salat,banka
SAL-MAS-007,Zaytun yashil,oshxona,salat,banka
SAL-MAS-008,Konserva makkajo'xori,oshxona,salat,banka
SAL-MAS-009,Konserva no'xat,oshxona,salat,banka
SAL-MAS-010,Marinadlangan bodring,oshxona,salat,banka
SAL-MAS-011,Qisqichbaqa tayoqchasi,oshxona,salat,kg
SAL-MAS-012,Qo'ziqorin,oshxona,salat,kg
SAL-MAS-013,Tovuq filesi,oshxona,salat,kg
NON-NAN-001,Obi non,non,pishiriq,dona
NON-NAN-002,Patir non,non,pishiriq,dona
NON-NAN-003,Bag'et non,non,pishiriq,dona
NON-NAN-004,Lavash,non,pishiriq,dona
NON-NAN-005,Somsa xamiri,non,pishiriq,kg
NON-NAN-006,Somsa tayyor,non,pishiriq,dona
NON-NAN-007,Pirojki,non,pishiriq,dona
NON-NAN-008,Chuchvara,non,pishiriq,kg
NON-NAN-009,Manti,non,pishiriq,kg
ICH-SUV-001,Mineral suv 0.5L,ichimlik,suv,dona
ICH-SUV-002,Mineral suv 1L,ichimlik,suv,dona
ICH-SUV-003,Mineral suv 1.5L,ichimlik,suv,dona
ICH-SUV-004,Oddiy suv 0.5L,ichimlik,suv,dona
ICH-SUV-005,Oddiy suv 1L,ichimlik,suv,dona
ICH-SUV-006,Oddiy suv 1.5L,ichimlik,suv,dona
ICH-GAZ-001,Coca-Cola 0.5L,ichimlik,gazli,dona
ICH-GAZ-002,Coca-Cola 1L,ichimlik,gazli,dona
ICH-GAZ-003,Coca-Cola 1.5L,ichimlik,gazli,dona
ICH-GAZ-004,Fanta 0.5L,ichimlik,gazli,dona
ICH-GAZ-005,Fanta 1L,ichimlik,gazli,dona
ICH-GAZ-006,Fanta 1.5L,ichimlik,gazli,dona
ICH-GAZ-007,Sprite 0.5L,ichimlik,gazli,dona
ICH-GAZ-008,Sprite 1L,ichimlik,gazli,dona
ICH-GAZ-009,Sprite 1.5L,ichimlik,gazli,dona
ICH-GAZ-010,Pepsi 0.5L,ichimlik,gazli,dona
ICH-GAZ-011,Pepsi 1L,ichimlik,gazli,dona
ICH-GAZ-012,Pepsi 1.5L,ichimlik,gazli,dona
ICH-SHARBAT-001,Olma sharbati 1L,ichimlik,sharbat,dona
ICH-SHARBAT-002,Apelsin sharbati 1L,ichimlik,sharbat,dona
ICH-SHARBAT-003,Shaftoli sharbati 1L,ichimlik,sharbat,dona
ICH-SHARBAT-004,Anor sharbati 1L,ichimlik,sharbat,dona
ICH-SHARBAT-005,Multimeva sharbati 1L,ichimlik,sharbat,dona
ICH-ISSIQ-001,Qora choy,ichimlik,issiq,kg
ICH-ISSIQ-002,Ko'k choy,ichimlik,issiq,kg
ICH-ISSIQ-003,Qahva,ichimlik,issiq,kg
ICH-ISSIQ-004,Shakar,ichimlik,issiq,kg
ICH-ISSIQ-005,Qaymoq,ichimlik,issiq,litr
ICH-ISSIQ-006,Sut,ichimlik,issiq,litr
SUT-SUT-001,Sut,oshxona,sut,litr
SUT-SUT-002,Qatiq,oshxona,sut,litr
SUT-SUT-003,Smetana,oshxona,sut,kg
SUT-SUT-004,Suzma,oshxona,sut,kg
SUT-SUT-005,Tvorog,oshxona,sut,kg
KON-BAN-001,Shprota,oshxona,konserva,banka
KON-BAN-002,Tunets konserva,oshxona,konserva,banka
KON-BAN-003,Ananas konserva,oshxona,konserva,banka
KON-BAN-004,Gilos konserva,oshxona,konserva,banka
KON-BAN-005,Jam,oshxona,konserva,banka
QAN-SHI-001,Konfet assorti,qandolat,shirinlik,kg
QAN-SHI-002,Pechenye,qandolat,shirinlik,kg
QAN-SHI-003,Vafli,qandolat,shirinlik,kg
QAN-SHI-004,Tort,qandolat,shirinlik,dona
QAN-SHI-005,Pirojnoe,qandolat,shirinlik,dona
QAN-SHI-006,Asal,qandolat,shirinlik,kg
QAN-SHI-007,Shakar kukuni,qandolat,shirinlik,kg
QAN-SHI-008,Kakao,qandolat,shirinlik,kg
QAN-SHI-009,Vanilin,qandolat,shirinlik,paket
BIR-IDO-001,Bir martalik stakan 200ml,bir_martalik,idish,dona
BIR-IDO-002,Bir martalik stakan 250ml,bir_martalik,idish,dona
BIR-IDO-003,Bir martalik tarelka kichik,bir_martalik,idish,dona
BIR-IDO-004,Bir martalik tarelka katta,bir_martalik,idish,dona
BIR-IDO-005,Bir martalik qoshiq,bir_martalik,idish,dona
BIR-IDO-006,Bir martalik vilka,bir_martalik,idish,dona
BIR-IDO-007,Bir martalik pichoq,bir_martalik,idish,dona
BIR-IDO-008,Salfetka,bir_martalik,idish,paket
BIR-IDO-009,Qog'oz sochiq,bir_martalik,idish,rulon
BIR-IDO-010,Folga,bir_martalik,idish,rulon
BIR-IDO-011,Plenka,bir_martalik,idish,rulon
BIR-IDO-012,Paket,bir_martalik,idish,dona
BIR-IDO-013,Musor paketi,bir_martalik,idish,dona
XOJ-TOZ-001,Idish yuvish suyuqligi,xojalik,tozalash,dona
XOJ-TOZ-002,Kir yuvish kukuni,xojalik,tozalash,kg
XOJ-TOZ-003,Oqartirgich,xojalik,tozalash,litr
XOJ-TOZ-004,Pol yuvish vositasi,xojalik,tozalash,litr
XOJ-TOZ-005,Oyna tozalagich,xojalik,tozalash,litr
XOJ-TOZ-006,Dezinfeksiya vositasi,xojalik,tozalash,litr
XOJ-TOZ-007,Sovun,xojalik,tozalash,dona
XOJ-TOZ-008,Suyuq sovun,xojalik,tozalash,litr
XOJ-TOZ-009,Gubka,xojalik,tozalash,dona
XOJ-TOZ-010,Latta,xojalik,tozalash,dona
XOJ-TOZ-011,Shvabra,xojalik,tozalash,dona
XOJ-TOZ-012,Supurgi,xojalik,tozalash,dona
DEK-GUL-001,Sun'iy gul,dekor,dekor,dona
DEK-GUL-002,Tirik gul,dekor,dekor,dona
DEK-GUL-003,Shar,dekor,dekor,dona
DEK-GUL-004,Lenta,dekor,dekor,rulon
DEK-GUL-005,Stol bezagi,dekor,dekor,dona
DEK-GUL-006,Sahna bezagi,dekor,dekor,komplekt
DEK-GUL-007,Fotoszona aksessuari,dekor,dekor,komplekt
SERV-XIZ-001,Ofitsiant xizmati,servis,xizmat,nafar
SERV-XIZ-002,Oshpaz xizmati,servis,xizmat,nafar
SERV-XIZ-003,Idish-tovoq ijarasi,servis,xizmat,komplekt
SERV-XIZ-004,Stol-stul ijarasi,servis,xizmat,komplekt
SERV-XIZ-005,DJ xizmati,servis,xizmat,xizmat
SERV-XIZ-006,Foto xizmati,servis,xizmat,xizmat
SERV-XIZ-007,Video xizmati,servis,xizmat,xizmat
SERV-XIZ-008,Boshlovchi xizmati,servis,xizmat,xizmat
CSV;

        collect(preg_split('/\r\n|\r|\n/', trim($csv)))
            ->filter()
            ->each(function (string $row) {
                [$sku, $name, $category, $subcategory, $unit] = str_getcsv($row);

                Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'name' => $name,
                        'category' => $category,
                        'subcategory' => $subcategory,
                        'unit' => $unit,
                        'is_active' => true,
                        'minimum_stock' => 0,
                        'current_stock' => Product::where('sku', $sku)->value('current_stock') ?? 0,
                        'last_purchase_price' => Product::where('sku', $sku)->value('last_purchase_price') ?? 0,
                    ]
                );
            });
    }
}
