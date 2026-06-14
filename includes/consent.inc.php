<?php
// includes/consent.inc.php
// Minimal consent banner include. Uses /consent.php endpoint.
?>
<div id="consent-banner" class="consent-banner" aria-live="polite" role="dialog" aria-label="Cookie consent preferences">
  <div class="consent-banner__content">
    <div class="consent-banner__text">
      <strong>Cookies on GB12i</strong>
      <p>We use cookies to keep the site working and to improve your experience. You can accept optional analytics cookies or reject them while keeping essential cookies enabled.</p>
    </div>
    <div class="consent-banner__actions">
      <button class="consent-banner__button consent-banner__button--secondary" id="consent-reject">Reject</button>
      <button class="consent-banner__button consent-banner__button--primary" id="consent-accept">Accept</button>
    </div>
  </div>
</div>
<script>
function __gb_getCookie(name) {
  const match = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
  return match ? match.pop() : null;
}
function __gb_showConsentBanner() {
  const banner = document.getElementById('consent-banner');
  if (banner) {
    banner.style.display = 'flex';
    requestAnimationFrame(() => banner.classList.add('consent-banner--visible'));
  }
}
function __gb_hideConsentBanner() {
  const banner = document.getElementById('consent-banner');
  if (banner) {
    banner.classList.remove('consent-banner--visible');
    setTimeout(() => { if (banner) banner.style.display = 'none'; }, 250);
  }
}
if (__gb_getCookie('consent') === null) {
  document.addEventListener('DOMContentLoaded', __gb_showConsentBanner);
}
function __gb_postConsent(action) {
  fetch('/consent.php', {
    method: 'POST',
    credentials: 'include',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({action})
  }).then(() => __gb_hideConsentBanner()).catch(() => __gb_hideConsentBanner());
}
document.addEventListener('click', function(e) {
  if (!e.target) return;
  if (e.target.id === 'consent-accept') {
    __gb_postConsent('accept');
  }
  if (e.target.id === 'consent-reject') {
    __gb_postConsent('reject');
  }
});
</script>
