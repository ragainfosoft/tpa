<?php
// =====================================================
// TPA IMS — Shared dropdown / data helpers
// Always read from DB — no hardcoded lists
// =====================================================

/**
 * Active branches for centre dropdowns.
 * Returns [['id'=>1,'name'=>'Chadwell Heath'], ...]
 */
function getBranches(bool $includeOnline = true, bool $includeNoPreference = false): array {
    static $cache = null;
    if ($cache === null) {
        try {
            $cache = getDB()->query("SELECT id, name FROM branches WHERE is_active = 1 ORDER BY sort_order, name")
                           ->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $cache = [['id'=>0,'name'=>'Chadwell Heath'],['id'=>0,'name'=>'Chelmsford'],['id'=>0,'name'=>'Online']];
        }
    }
    $result = $cache;
    if (!$includeOnline) {
        $result = array_filter($result, fn($b) => strtolower($b['name']) !== 'online');
    }
    if ($includeNoPreference) {
        array_unshift($result, ['id'=>0,'name'=>'No preference']);
    }
    return array_values($result);
}

/**
 * Branch names only — for simple foreach loops.
 */
function getBranchNames(bool $includeNoPreference = false): array {
    $names = array_column(getBranches(true, false), 'name');
    if ($includeNoPreference) array_unshift($names, 'No preference');
    return $names;
}

/**
 * Active programmes for course_interest dropdowns.
 */
function getProgrammes(): array {
    static $cache = null;
    if ($cache === null) {
        try {
            $cache = getDB()->query("SELECT id, name, short_code, year_range FROM programmes WHERE is_active = 1 ORDER BY sort_order, name")
                           ->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $cache = [
                ['id'=>0,'name'=>'11 Plus Preparation','short_code'=>'11plus','year_range'=>'Year 3–6'],
                ['id'=>0,'name'=>'SATs (Year 6)',       'short_code'=>'sats_y6','year_range'=>'Year 6'],
                ['id'=>0,'name'=>'Key Stage 1 (Year 1–2)','short_code'=>'ks1','year_range'=>'Year 1–2'],
                ['id'=>0,'name'=>'Key Stage 2 (Year 3–6)','short_code'=>'ks2','year_range'=>'Year 3–6'],
                ['id'=>0,'name'=>'Key Stage 3 (Year 7–9)','short_code'=>'ks3','year_range'=>'Year 7–9'],
                ['id'=>0,'name'=>'GCSE (Year 10–11)',    'short_code'=>'gcse','year_range'=>'Year 10–11'],
                ['id'=>0,'name'=>'Not Sure',             'short_code'=>'not_sure','year_range'=>null],
            ];
        }
    }
    return $cache;
}

/**
 * Active lead sources.
 */
function getLeadSources(): array {
    static $cache = null;
    if ($cache === null) {
        try {
            $cache = array_column(
                getDB()->query("SELECT name FROM lead_sources WHERE is_active = 1 ORDER BY sort_order, name")->fetchAll(PDO::FETCH_ASSOC),
                'name'
            );
        } catch (Exception $e) {
            $cache = ['Google Search','Word of Mouth','Social Media','Flyer / Leaflet','Website','Referral','Other'];
        }
    }
    return $cache;
}

/**
 * Year groups — configurable range.
 * Returns ['Reception','Year 1','Year 2', ..., 'Year 13','Adult']
 */
function getYearGroups(): array {
    $groups = ['Reception'];
    for ($i = 1; $i <= 13; $i++) $groups[] = "Year $i";
    $groups[] = 'Adult';
    return $groups;
}

/**
 * Course types for batch dropdowns (short codes).
 * Falls back to a sensible default set.
 */
function getCourseTypes(): array {
    try {
        $rows = getDB()->query("SELECT short_code as code, name FROM programmes WHERE is_active=1 AND short_code NOT IN ('not_sure','') ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) return $rows;
    } catch (Exception $e) {}
    return [
        ['code'=>'11plus','name'=>'11 Plus'],
        ['code'=>'sats_y2','name'=>'SATs Y2'],
        ['code'=>'sats_y6','name'=>'SATs Y6'],
        ['code'=>'ks1','name'=>'KS1'],
        ['code'=>'ks2','name'=>'KS2'],
        ['code'=>'ks3','name'=>'KS3'],
        ['code'=>'gcse','name'=>'GCSE'],
        ['code'=>'alevel','name'=>'A-Level'],
        ['code'=>'adult','name'=>'Adult'],
        ['code'=>'summer_camp','name'=>'Summer Camp'],
        ['code'=>'easter_camp','name'=>'Easter Camp'],
        ['code'=>'other','name'=>'Other'],
    ];
}

/**
 * Render a <select> of branches.
 * $current  — currently selected value (name string)
 * $name     — form field name
 * $extras   — extra HTML attributes on the <select>
 * $noPreference — include a "No preference" option at top
 */
function branchSelect(string $name, string $current = '', string $extras = '', bool $noPreference = false): string {
    $branches = getBranches(true, $noPreference);
    $html = "<select name=\"$name\" class=\"form-select\" $extras>";
    if ($noPreference) {
        $sel = ($current === '' || $current === 'No preference') ? ' selected' : '';
        $html .= "<option value=\"No preference\"$sel>No preference</option>";
    }
    foreach ($branches as $b) {
        if ($b['name'] === 'No preference') continue;
        $sel = ($b['name'] === $current) ? ' selected' : '';
        $html .= "<option value=\"" . htmlspecialchars($b['name'], ENT_QUOTES) . "\"$sel>" . htmlspecialchars($b['name'], ENT_QUOTES) . "</option>";
    }
    $html .= "</select>";
    return $html;
}

/**
 * Render a <select> of programmes.
 */
function programmeSelect(string $name, string $current = '', string $extras = ''): string {
    $html = "<select name=\"$name\" class=\"form-select\" $extras>";
    $html .= "<option value=\"\">Select programme…</option>";
    foreach (getProgrammes() as $p) {
        $sel = ($p['name'] === $current) ? ' selected' : '';
        $label = htmlspecialchars($p['name'], ENT_QUOTES);
        if ($p['year_range']) $label .= " <small class='text-muted'>({$p['year_range']})</small>";
        $html .= "<option value=\"" . htmlspecialchars($p['name'], ENT_QUOTES) . "\"$sel>" . htmlspecialchars($p['name'], ENT_QUOTES) . ($p['year_range'] ? " ({$p['year_range']})" : '') . "</option>";
    }
    $html .= "</select>";
    return $html;
}
