<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LanguageLine;
use App\Services\SuperAdmin\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageController extends Controller
{
    private const FIELDS = [
        'brand_tagline' => 'Brend tagline',
        'landing_meta_title' => 'Landing meta title',
        'landing_meta_description' => 'Landing meta description',
        'landing_nav_product' => 'Landing nav product',
        'landing_nav_benefits' => 'Landing nav benefits',
        'landing_nav_pricing' => 'Landing nav pricing',
        'landing_nav_contact' => 'Landing nav contact',
        'landing_nav_login' => 'Landing login',
        'landing_nav_register' => 'Landing register',
        'landing_language_switcher' => 'Til switcher aria',
        'landing_hero_trust_trial' => 'Hero trust 1',
        'landing_hero_trust_setup' => 'Hero trust 2',
        'landing_hero_trust_demo' => 'Hero trust 3',
        'landing_preview_product_name' => 'Landing preview product name',
        'landing_preview_badge' => 'Landing preview badge',
        'landing_preview_chart_period' => 'Landing preview chart period',
        'landing_preview_calendar_period' => 'Landing preview calendar period',
        'landing_preview_table_items' => 'Landing preview table items',
        'landing_preview_pipeline_period' => 'Landing preview pipeline period',
        'landing_preview_heading' => 'Landing section eyebrow preview',
        'landing_problem_heading' => 'Landing section eyebrow problem',
        'landing_transform_heading' => 'Landing section eyebrow transform',
        'landing_transform_title' => 'Landing transform title',
        'landing_solution_heading' => 'Landing section eyebrow solution',
        'landing_benefits_heading' => 'Landing section eyebrow benefits',
        'landing_features_heading' => 'Landing section eyebrow features',
        'landing_revenue_heading' => 'Landing section eyebrow revenue',
        'landing_social_heading' => 'Landing section eyebrow social',
        'landing_pricing_heading' => 'Landing section eyebrow pricing',
        'landing_contact_heading' => 'Landing section eyebrow contact',
        'landing_transform_before' => 'Landing before label',
        'landing_transform_after' => 'Landing after label',
        'landing_transform_item_1_before' => 'Landing transform item 1 before',
        'landing_transform_item_1_after' => 'Landing transform item 1 after',
        'landing_transform_item_2_before' => 'Landing transform item 2 before',
        'landing_transform_item_2_after' => 'Landing transform item 2 after',
        'landing_transform_item_3_before' => 'Landing transform item 3 before',
        'landing_transform_item_3_after' => 'Landing transform item 3 after',
        'landing_preview_chart_range' => 'Landing preview chart range',
        'landing_preview_crm_booking' => 'Landing preview CRM booking label',
        'landing_preview_realtime' => 'Landing preview realtime label',
        'landing_preview_placeholder_dashboard' => 'Landing preview placeholder dashboard',
        'landing_preview_placeholder_admin' => 'Landing preview placeholder admin',
        'landing_preview_placeholder_analytics' => 'Landing preview placeholder analytics',
        'landing_cta_point_demo' => 'Landing CTA point 1',
        'landing_cta_point_trial' => 'Landing CTA point 2',
        'landing_cta_point_consultation' => 'Landing CTA point 3',
        'landing_contact_phone_label' => 'Landing contact phone label',
        'landing_contact_telegram_label' => 'Landing contact telegram label',
        'landing_footer_product' => 'Landing footer product',
        'landing_footer_company' => 'Landing footer company',
        'landing_footer_rights_suffix' => 'Landing footer rights suffix',
        'auth_panel_label' => 'Auth panel label',
        'auth_rights' => 'Auth footer rights',
        'auth_login_heading' => 'Login sarlavha',
        'auth_login_subtitle' => 'Login izoh',
        'auth_login_username_label' => 'Login username label',
        'auth_login_password_label' => 'Login password label',
        'auth_login_remember' => 'Login remember me',
        'auth_login_submit' => 'Login submit',
        'auth_login_show_password' => 'Login show password',
        'auth_login_hide_password' => 'Login hide password',
        'auth_register_pending_badge' => 'Register badge',
        'auth_register_visual_tag' => 'Register visual tag',
        'auth_register_heading' => 'Register sarlavha',
        'auth_register_subtitle' => 'Register izoh',
        'auth_register_visual_heading' => 'Register visual heading',
        'auth_register_visual_text' => 'Register visual text',
        'auth_register_first_name' => 'Register first name',
        'auth_register_last_name' => 'Register last name',
        'auth_register_username' => 'Register username',
        'auth_register_phone' => 'Register phone',
        'auth_register_restaurant_name' => 'Register restaurant name',
        'auth_register_message' => 'Register message',
        'auth_register_terms' => 'Register terms',
        'auth_register_submit' => 'Register submit',
        'auth_register_has_account' => 'Register has account',
        'auth_register_login_link' => 'Register login link',
        'auth_register_feature_trial_title' => 'Register feature 1 title',
        'auth_register_feature_trial_text' => 'Register feature 1 text',
        'auth_register_feature_setup_title' => 'Register feature 2 title',
        'auth_register_feature_setup_text' => 'Register feature 2 text',
        'auth_register_feature_demo_title' => 'Register feature 3 title',
        'auth_register_feature_demo_text' => 'Register feature 3 text',
    ];

    public function edit(Request $request): View
    {
        $locale = in_array($request->query('lang', 'uz'), ['uz', 'uzc', 'ru', 'en'], true) ? $request->query('lang', 'uz') : 'uz';

        return view('superadmin.languages.edit', [
            'pageTitle' => 'Tillar',
            'locale' => $locale,
            'fields' => self::FIELDS,
            'lines' => LanguageLine::query()
                ->where('locale', $locale)
                ->pluck('value', 'key'),
        ]);
    }

    public function update(Request $request, AuditLogService $audit): RedirectResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:uz,uzc,ru,en'],
            'lines' => ['array'],
        ]);

        foreach (array_keys(self::FIELDS) as $key) {
            $value = trim((string) data_get($data, "lines.$key", ''));

            if ($value === '') {
                LanguageLine::query()
                    ->where('locale', $data['locale'])
                    ->where('key', $key)
                    ->delete();
                continue;
            }

            LanguageLine::updateOrCreate(
                ['locale' => $data['locale'], 'key' => $key],
                ['value' => $value],
            );
        }

        LanguageLine::flushGroupedCache();

        $audit->record('language.lines.updated', null, null, ['locale' => $data['locale']], 'info', $request, 'Translations '.$data['locale']);

        return redirect()
            ->route('superadmin.languages.edit', ['lang' => $data['locale']])
            ->with('success', 'Til matnlari saqlandi.');
    }
}
