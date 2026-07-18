/* Papier documentation — theme toggle, search, copy buttons, scroll spy. */

(function () {
  'use strict';

  var root = document.documentElement;

  // ── Theme ─────────────────────────────────────────────────────────────────

  function currentTheme() {
    return root.getAttribute('data-theme') ||
      (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  }

  var themeBtn = document.querySelector('.theme-toggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      var next = currentTheme() === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      localStorage.setItem('papier-theme', next);
    });
  }

  // ── Mobile navigation ─────────────────────────────────────────────────────

  var sidebar = document.querySelector('.sidebar');
  var menuBtn = document.querySelector('.menu-toggle');

  if (menuBtn && sidebar) {
    menuBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      sidebar.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (sidebar.classList.contains('open') && !sidebar.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  // ── Copy buttons ──────────────────────────────────────────────────────────

  document.querySelectorAll('.code-block .copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var code = btn.parentElement.querySelector('code');
      navigator.clipboard.writeText(code.innerText).then(function () {
        btn.textContent = 'Copied';
        btn.classList.add('done');
        setTimeout(function () {
          btn.textContent = 'Copy';
          btn.classList.remove('done');
        }, 1600);
      });
    });
  });

  // ── Heading anchors ───────────────────────────────────────────────────────

  document.querySelectorAll('.content h2[id], .content h3[id]').forEach(function (h) {
    var a = document.createElement('a');
    a.className = 'anchor';
    a.href = '#' + h.id;
    a.textContent = '#';
    a.setAttribute('aria-label', 'Link to this section');
    h.appendChild(a);
  });

  // ── Scroll spy for the "On this page" rail ────────────────────────────────

  var tocLinks = Array.prototype.slice.call(document.querySelectorAll('.toc a'));

  if (tocLinks.length) {
    var targets = tocLinks
      .map(function (a) { return document.getElementById(a.hash.slice(1)); })
      .filter(Boolean);

    var spy = function () {
      var limit = window.scrollY + 120;
      var active = targets[0];

      targets.forEach(function (t) {
        if (t.offsetTop <= limit) active = t;
      });

      tocLinks.forEach(function (a) {
        a.classList.toggle('active', active && a.hash === '#' + active.id);
      });
    };

    var ticking = false;
    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () { spy(); ticking = false; });
    }, { passive: true });
    spy();
  }

  // ── Search ────────────────────────────────────────────────────────────────

  var input   = document.getElementById('search-input');
  var results = document.getElementById('search-results');
  var index   = null;
  var shown   = [];
  var cursor  = -1;

  if (!input || !results) return;

  function loadIndex() {
    if (index) return Promise.resolve(index);
    return fetch('assets/search-index.json')
      .then(function (r) { return r.json(); })
      .then(function (data) { index = data; return index; })
      .catch(function () { index = []; return index; });
  }

  /** Score an entry: prefix match beats word-start beats substring. */
  function score(entry, q) {
    var t = entry.t.toLowerCase();
    if (t === q) return 100;
    if (t.indexOf(q) === 0) return 80;
    if (t.indexOf(' ' + q) > -1) return 60;
    if (t.indexOf(q) > -1) return 40;
    if (entry.s && entry.s.toLowerCase().indexOf(q) > -1) return 15;
    return 0;
  }

  function render(matches, q) {
    shown = matches;
    cursor = -1;

    if (!q) {
      results.classList.remove('open');
      return;
    }

    if (!matches.length) {
      results.innerHTML = '<div class="empty">No results for “' +
        q.replace(/[<>&]/g, '') + '”</div>';
      results.classList.add('open');
      return;
    }

    results.innerHTML = matches.map(function (m) {
      return '<a href="' + m.u + '">' + m.t +
        '<span class="r-page">' + m.p + '</span></a>';
    }).join('');
    results.classList.add('open');
  }

  function search() {
    var q = input.value.trim().toLowerCase();
    if (!q) { render([], ''); return; }

    loadIndex().then(function (data) {
      var matches = data
        .map(function (e) { return { e: e, s: score(e, q) }; })
        .filter(function (m) { return m.s > 0; })
        .sort(function (a, b) { return b.s - a.s; })
        .slice(0, 12)
        .map(function (m) { return m.e; });

      render(matches, q);
    });
  }

  function move(delta) {
    var links = results.querySelectorAll('a');
    if (!links.length) return;

    cursor = (cursor + delta + links.length) % links.length;
    links.forEach(function (a, i) { a.classList.toggle('sel', i === cursor); });
    links[cursor].scrollIntoView({ block: 'nearest' });
  }

  input.addEventListener('input', search);
  input.addEventListener('focus', function () { if (input.value) search(); });

  input.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowDown')      { e.preventDefault(); move(1); }
    else if (e.key === 'ArrowUp')   { e.preventDefault(); move(-1); }
    else if (e.key === 'Enter')     {
      var sel = results.querySelector('a.sel') || results.querySelector('a');
      if (sel) { e.preventDefault(); location.href = sel.href; }
    }
    else if (e.key === 'Escape')    { input.blur(); results.classList.remove('open'); }
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.search')) results.classList.remove('open');
  });

  // "/" focuses the search box, as on most documentation sites.
  document.addEventListener('keydown', function (e) {
    if (e.key === '/' && document.activeElement !== input &&
        !/^(INPUT|TEXTAREA)$/.test(document.activeElement.tagName)) {
      e.preventDefault();
      input.focus();
    }
  });
})();
