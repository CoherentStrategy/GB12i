<?php
// includes/consent.inc.php
// Minimal consent banner include. Uses /consent.php endpoint.
?>
<div id="consent-banner" style="display:none; position: fixed; bottom: 12px; left: 12px; right:12px; min-height: 100px; background: rgba(255,255,255,0.9) url('img/banner-bg.png') center/cover no-repeat; border:1px solid rgba(221,221,221,0.9); padding:18px; box-shadow:0 4px 12px rgba(0,0,0,0.15); display:flex; gap:12px; align-items:center; z-index:9999; border-radius:12px; backdrop-filter: blur(6px);">
    <div style="flex:1; font-size: 1rem; line-height: 1.5;">We use cookies to improve the site. Accept optional analytics?</div>
    <button id="consent-accept" style="padding:12px 18px; font-size:0.95rem; border:none; border-radius:8px; background:#245bc8; color:#fff; cursor:pointer;">Accept</button>
    <button id="consent-reject" style="padding:12px 18px; font-size:0.95rem; border:1px solid rgba(0,0,0,0.12); border-radius:8px; background:rgba(255,255,255,0.85); cursor:pointer;">Reject</button>
</div>
<script>
function __gb_getCookie(name) {
    const v = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
    return v ? v.pop() : null;
}
if (__gb_getCookie('consent') === null) {
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('consent-banner').style.display = 'flex';
    });
}
function __gb_postConsent(action){
    fetch('/consent.php', {
        method: 'POST',
        credentials: 'include',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action})
    }).then(()=>{ document.getElementById('consent-banner').style.display='none'; });
}
document.addEventListener('click', function(e){
    if (e.target && e.target.id === 'consent-accept') __gb_postConsent('accept');
    if (e.target && e.target.id === 'consent-reject') __gb_postConsent('reject');
});
</script>

<!-- Preferences Center -->
<div id="preferences-center" class="preferences-center" aria-hidden="true">
    <div class="preferences-center__backdrop" id="prefs-backdrop"></div>
    <div class="preferences-center__panel" role="dialog" aria-modal="true" aria-label="Cookie preferences">
        <header>
            <h2>Cookie Preferences</h2>
            <button id="prefs-close" aria-label="Close preferences">×</button>
        </header>
        <main>
            <p>Manage which cookies you allow. Essential cookies are always required.</p>
            <div class="pref-row">
                <label>Essential cookies</label>
                <input type="checkbox" disabled checked>
            </div>
            <div class="pref-row">
                <label for="pref-analytics">Analytics</label>
                <input type="checkbox" id="pref-analytics">
            </div>
        </main>
        <footer>
            <button id="prefs-cancel" class="btn">Cancel</button>
            <button id="prefs-save" class="btn btn-primary">Save preferences</button>
        </footer>
    </div>
</div>
<script>
// Open preferences center when user clicks the link
document.addEventListener('DOMContentLoaded', function(){
    const openLink = document.getElementById('open_preferences_center');
    const panel = document.getElementById('preferences-center');
    const backdrop = document.getElementById('prefs-backdrop');
    const closeBtn = document.getElementById('prefs-close');
    const cancelBtn = document.getElementById('prefs-cancel');
    const saveBtn = document.getElementById('prefs-save');
    const analyticsChk = document.getElementById('pref-analytics');

    function showPrefs(){
        // populate current state from cookie
        analyticsChk.checked = document.cookie.indexOf('analytics=1') !== -1;
        panel.setAttribute('aria-hidden','false');
        document.body.style.overflow = 'hidden';
    }
    function hidePrefs(){
        panel.setAttribute('aria-hidden','true');
        document.body.style.overflow = '';
    }

    if (openLink) openLink.addEventListener('click', function(e){ e.preventDefault(); showPrefs(); });
    if (backdrop) backdrop.addEventListener('click', hidePrefs);
    if (closeBtn) closeBtn.addEventListener('click', hidePrefs);
    if (cancelBtn) cancelBtn.addEventListener('click', hidePrefs);

    if (saveBtn) saveBtn.addEventListener('click', function(){
        const analytics = !!analyticsChk.checked;
        fetch('/consent.php', {
            method: 'POST',
            credentials: 'include',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'set', preferences: { analytics } })
        }).then(() => {
            hidePrefs();
            // also hide banner if present
            const b = document.getElementById('consent-banner'); if (b) b.style.display = 'none';
        }).catch(() => hidePrefs());
    });

    // keyboard escape
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hidePrefs(); });
});
</script>
