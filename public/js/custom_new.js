// SPACare custom.js — v8.2 (seguro)
// 1) Menú móvil (hamburguesa + overlay)
// 2) Item activo del menú: texto blanco + fondo verde (con fallback por URL)

(() => {
  if (window.__spCustomVer === '8.2') return;
  window.__spCustomVer = '8.2';

  document.addEventListener('DOMContentLoaded', () => {
    try {
      // -------------------------------
      // 1) MENÚ MÓVIL
      // -------------------------------
      const MQL = window.matchMedia('(max-width: 768px)');
      const SEL_HEADER  = 'header, .layout-navbar, .navbar, header.navbar, .layout-header, .topbar';
      const SEL_SIDEBAR = 'aside.sidebar, aside.layout-menu, aside.moonshine-menu';

      const header  = document.querySelector(SEL_HEADER);
      const sidebar = document.querySelector(SEL_SIDEBAR);

      if (header && sidebar) {
        // Limpia restos antiguos (si los hubiera)
        document.querySelectorAll('.spc-hamburger, .spc-hamburger-float').forEach(el => el.remove());

        // Crea botón hamburger si no existe
        let btn = header.querySelector('.spc-hamburger');
        if (!btn) {
          btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'spc-hamburger';
          btn.setAttribute('aria-label', 'Abrir menú');
          btn.setAttribute('aria-expanded', 'false');
          btn.style.cssText = 'display:inline-flex;align-items:center;gap:.5rem;background:transparent;border:0;cursor:pointer;padding:.5rem;margin:.25rem 0;';
          btn.innerHTML =
            '<span style="width:22px;height:2px;background:currentColor;display:block;box-shadow:0 6px 0 currentColor,0 -6px 0 currentColor"></span>' +
            '<span style="font-size:14px">Menú</span>';
          header.insertBefore(btn, header.firstChild);
        }

        // Overlay oscuro clicable
        function ensureOverlay() {
          var ov = document.querySelector('.sidebar-backdrop');
          if (!ov) {
            ov = document.createElement('div');
            ov.className = 'sidebar-backdrop';
            ov.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:999;opacity:0;pointer-events:none;transition:opacity .2s ease;';
            document.body.appendChild(ov);
          }
          return ov;
        }
        const overlay = ensureOverlay();

        function openSidebar() {
          document.body.classList.add('sidebar-open');
          overlay.style.opacity = '1';
          overlay.style.pointerEvents = 'auto';
          btn.setAttribute('aria-expanded', 'true');
        }
        function closeSidebar() {
          document.body.classList.remove('sidebar-open');
          overlay.style.opacity = '0';
          overlay.style.pointerEvents = 'none';
          btn.setAttribute('aria-expanded', 'false');
        }
        function toggleSidebar() {
          if (document.body.classList.contains('sidebar-open')) closeSidebar();
          else openSidebar();
        }

        btn.addEventListener('click', function (e) {
          e.preventDefault();
          if (!MQL.matches) return; // Solo móvil
          toggleSidebar();
        });
        overlay.addEventListener('click', closeSidebar);

        function handleMediaChange() {
          if (!MQL.matches) {
            // En escritorio, asegúrate de cerrar
            closeSidebar();
          }
        }
        handleMediaChange();
        if (typeof MQL.addEventListener === 'function') {
          MQL.addEventListener('change', handleMediaChange);
        } else if (typeof MQL.addListener === 'function') {
          // Safari antiguo
          MQL.addListener(handleMediaChange);
        }
      }
    } catch (err) {
      try { console.error('[SPACARE] mobile block error:', err); } catch (_) {}
    }

    try {
      // -------------------------------
      // 2) ITEM ACTIVO (BLANCO + VERDE)
      // -------------------------------
      var GREEN = '#004a29'; // tu primary (UNED)
      var CONTAINER_SEL = 'aside.sidebar, aside.layout-menu, aside.moonshine-menu';

      function ensureFallback(container) {
        // Si el tema no marca activo, marcamos por URL
        var hasActive = container.querySelector('.menu li.active > a, .menu a.active, .menu a[aria-current="page"], .menu [data-active="true"]');
        if (hasActive) return;

        var cur = location.pathname.replace(/\/+$/, '');
        var links = container.querySelectorAll('.menu a[href]');
        for (var i = 0; i < links.length; i++) {
          var a = links[i];
          try {
            var p = new URL(a.href, location.origin).pathname.replace(/\/+$/, '');
            if (cur === p || cur.indexOf(p + '/') === 0) {
              a.classList.add('spc-active');
              var li = a.closest ? a.closest('li') : null;
              if (li && li.classList) li.classList.add('spc-active');
            }
          } catch (_) {}
        }
      }

      function paintActive(container) {
        var sel =
          '.menu li.active > a, .menu a.active, .menu a[aria-current="page"], .menu [data-active="true"], ' +
          '.menu a.spc-active, .menu li.spc-active > a';

        var items = container.querySelectorAll(sel);
        for (var i = 0; i < items.length; i++) {
          var node = items[i];
          var link = node.tagName === 'A' ? node : (node.closest ? node.closest('a') : null);
          if (!link) continue;

          link.style.setProperty('background-color', GREEN, 'important');
          link.style.setProperty('color', '#fff', 'important');

          var kids = link.querySelectorAll('*');
          for (var k = 0; k < kids.length; k++) {
            var n = kids[k];
            n.style.setProperty('color', '#fff', 'important');
            if (n.tagName && n.tagName.toLowerCase() === 'svg') {
              n.style.setProperty('fill', '#fff', 'important');
              n.style.setProperty('stroke', '#fff', 'important');
            }
          }
        }
      }

      function runActivePaint() {
        var container = document.querySelector(CONTAINER_SEL);
        if (!container) return;
        ensureFallback(container);
        paintActive(container);
      }

      // Ejecuta y reintenta por si el menú llega tarde
      runActivePaint();
      setTimeout(runActivePaint, 0);
      setTimeout(runActivePaint, 150);
      setTimeout(runActivePaint, 500);

      // Si navegas con back/forward del navegador
      window.addEventListener('popstate', runActivePaint);
      window.addEventListener('load', runActivePaint);
    } catch (err2) {
      try { console.error('[SPACARE] active paint error:', err2); } catch (_) {}
    }
  });
})();
