// SPACare custom.js — v8.6 (con contraste mejorado)
// - Menú móvil
// - Grupo abierto: trigger en verde oscuro, items en fondo claro
// - Item activo: verde muy oscuro con texto blanco

(() => {
  if (window.__spCustomVer === '8.6c') return;
  window.__spCustomVer = '8.6c';
  try { console.log('[SPACARE] custom v8.6c loaded'); } catch(_) {}

  document.addEventListener('DOMContentLoaded', () => {
    // ---------------- MENÚ MÓVIL ----------------
    try {
      const MQL = window.matchMedia('(max-width: 768px)');
      const SEL_HEADER  = 'header, .layout-navbar, .navbar, header.navbar, .layout-header, .topbar';
      const SEL_SIDEBAR = 'aside.sidebar, aside.layout-menu, aside.moonshine-menu';
      const header  = document.querySelector(SEL_HEADER);
      const sidebar = document.querySelector(SEL_SIDEBAR);

      if (header && sidebar) {
        document.querySelectorAll('.spc-hamburger, .spc-hamburger-float').forEach(el => el.remove());
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
        function ensureOverlay() {
          let ov = document.querySelector('.sidebar-backdrop');
          if (!ov) {
            ov = document.createElement('div');
            ov.className = 'sidebar-backdrop';
            ov.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:999;opacity:0;pointer-events:none;transition:opacity .2s ease;';
            document.body.appendChild(ov);
          }
          return ov;
        }
        const overlay = ensureOverlay();
        function openSidebar() { document.body.classList.add('sidebar-open'); overlay.style.opacity='1'; overlay.style.pointerEvents='auto'; btn.setAttribute('aria-expanded','true'); }
        function closeSidebar(){ document.body.classList.remove('sidebar-open'); overlay.style.opacity='0'; overlay.style.pointerEvents='none'; btn.setAttribute('aria-expanded','false'); }
        function toggleSidebar(){ document.body.classList.contains('sidebar-open') ? closeSidebar() : openSidebar(); }
        btn.addEventListener('click', e => { e.preventDefault(); if (!MQL.matches) return; toggleSidebar(); });
        overlay.addEventListener('click', closeSidebar);
        function handleMediaChange(){ if (!MQL.matches) closeSidebar(); }
        handleMediaChange();
        if (typeof MQL.addEventListener === 'function') MQL.addEventListener('change', handleMediaChange);
        else if (typeof MQL.addListener === 'function') MQL.addListener(handleMediaChange);
      }
    } catch(e){ try{console.error('[SPACARE] mobile block error:', e);}catch(_){} }

    // --------------- COLORES DE MENÚ (contraste mejorado) ---------------
    try {
      const DARK  = '#00361e'; // muy oscuro para activo/trigger
      const LIGHT = '#e6f3ee'; // muy claro para fondo de submenú
      const CONTAINER_SEL = 'aside.sidebar, aside.layout-menu, aside.moonshine-menu';
      const ACTIVE_NATIVE = '.menu li.active > a, .menu a.active, .menu a[aria-current="page"], .menu [data-active="true"]';
      const ACTIVE_ALL    = ACTIVE_NATIVE + ', .menu a.spc-active, .menu li.spc-active > a';

      const setImp = (el,p,v)=>{ try{ el.style.setProperty(p,v,'important'); }catch(_){} };
      const clrImp = (el,p)=>{ try{ el.style.removeProperty(p); }catch(_){} };
      const isVisible = el => !!el && (t=window.getComputedStyle(el)) && t.display!=='none' && t.visibility!=='hidden' && el.offsetHeight>0;

      function whitenNode(n){
        setImp(n,'color','#fff'); setImp(n,'-webkit-text-fill-color','#fff');
        const t=(n.tagName||'').toLowerCase();
        if(t==='svg'||t==='path'){ setImp(n,'fill','#fff'); setImp(n,'stroke','#fff'); }
        if(t==='img'){ setImp(n,'filter','brightness(0) invert(1)'); }
      }
      function whitenTree(root){ whitenNode(root); root.querySelectorAll('*').forEach(whitenNode); }

      function markFallback(container){
        if (container.querySelector(ACTIVE_NATIVE)) return;
        const cur = location.pathname.replace(/\/+$/,'');
        container.querySelectorAll('.menu a[href]').forEach(a=>{
          try{
            const p = new URL(a.href, location.origin).pathname.replace(/\/+$/,'');
            if(cur===p || cur.startsWith(p+'/')) { a.classList.add('spc-active'); a.closest('li')?.classList.add('spc-active'); }
          }catch(_){}
        });
      }

      function paintOpenGroups(container){
        container.querySelectorAll('.menu li').forEach(li=>{
          let sub=null;
          for(const ch of li.children){
            if(ch.classList && (ch.classList.contains('menu-sub')||ch.classList.contains('submenu')||ch.classList.contains('menu-children')||ch.tagName.toLowerCase()==='ul')){ sub=ch; break; }
          }
          const trigger = li.querySelector(':scope > a, :scope > button, :scope > .menu-link, :scope > .menu-trigger, :scope > .menu-group-trigger');

          if(sub && isVisible(sub)){
            if (trigger){ setImp(trigger,'background-color',DARK); whitenTree(trigger); }

            setImp(sub,'background-color',LIGHT);
            sub.querySelectorAll('a').forEach(a=>{
              setImp(a,'background-color','transparent');
              setImp(a,'color','#206c52');
              a.querySelectorAll('svg').forEach(svg=>{
                setImp(svg,'color','currentColor'); setImp(svg,'fill','currentColor'); setImp(svg,'stroke','currentColor');
              });
            });
          } else {
            if (trigger) ['background-color','color','-webkit-text-fill-color','fill','stroke','filter'].forEach(p=>clrImp(trigger,p));
          }
        });
      }

      function paintActiveLinks(container){
        container.querySelectorAll(ACTIVE_ALL).forEach(node=>{
          const link = node.tagName==='A'?node:node.closest('a');
          if(!link) return;
          setImp(link,'background-color',DARK);
          setImp(link,'box-shadow','inset 0 0 0 2px rgba(255,255,255,.12)');
          whitenTree(link);
        });
      }

      function paintAll(){
        const container = document.querySelector(CONTAINER_SEL);
        if(!container) return;
        markFallback(container);
        paintOpenGroups(container);
        paintActiveLinks(container);
      }

      paintAll();
      setTimeout(paintAll, 80);
      setTimeout(paintAll, 200);
      setTimeout(paintAll, 600);

      const sidebar = document.querySelector(CONTAINER_SEL);
      if (sidebar){
        sidebar.addEventListener('click', () => setTimeout(paintAll, 0), true);
        const mo = new MutationObserver(() => setTimeout(paintAll, 0));
        mo.observe(sidebar, { subtree:true, childList:true, attributes:true, attributeFilter:['class','style','aria-expanded'] });
      }
      window.addEventListener('popstate', paintAll);
      window.addEventListener('load', paintAll);
      window.addEventListener('resize', () => setTimeout(paintAll, 0));
    } catch(e){ try{console.error('[SPACARE] menu color error:', e);}catch(_){} }
  });
})();

// === Login UX v8.6.1: colocar "¿Olvidaste tu contraseña?" debajo del botón ===
(() => {
  function moveForgot() {
    const form =
      document.querySelector('form[action*="login" i]') ||
      document.querySelector('form input[name="email"]')?.closest('form') ||
      document.querySelector('form');

    if (!form) return false;

    const submit = form.querySelector('button[type="submit"], [type="submit"]');
    if (!submit) return false;

    let forgot =
      form.querySelector('a[href*="password" i]') ||
      document.querySelector('a[href*="forgot" i], a[href*="reset" i], a[href*="password" i]') ||
      Array.from(document.querySelectorAll('a')).find(a => /olvid|forgot/i.test(a.textContent || ''));

    if (!forgot) return false;

    let wrap = form.querySelector('.ms-forgot-wrap');
    if (!wrap) {
      wrap = document.createElement('div');
      wrap.className = 'ms-forgot-wrap';
      wrap.style.cssText = 'margin-top:12px;display:flex;justify-content:center;align-items:center;width:100%';

      submit.insertAdjacentElement('afterend', wrap);
    } else if (wrap.previousElementSibling !== submit) {
      submit.insertAdjacentElement('afterend', wrap);
    }

    if (forgot.parentElement !== wrap) wrap.appendChild(forgot);
    Object.assign(forgot.style, {
      display: 'inline-block',
      fontSize: '.875rem',
      textDecoration: 'underline',
      whiteSpace: 'nowrap',
      marginTop: '6px'
    });

    const card = form.closest('.card, .box, .auth-card, .container, .panel') || form.parentElement;
    if (card) card.style.overflow = 'visible';

    console.log('[SPACARE] forgot link moved');
    return true;
  }

  document.addEventListener('DOMContentLoaded', () => {
    let ok = moveForgot();
    if (!ok) setTimeout(() => moveForgot() || setTimeout(moveForgot, 400), 120);

    const mo = new MutationObserver(() => moveForgot());
    mo.observe(document.body, { childList: true, subtree: true });
  });
})();
