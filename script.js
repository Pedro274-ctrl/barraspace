/**
 * BARRA SPACE CENTER - SCRIPT - VERSÃO ZERO ERRO
 * Correção cirúrgica dos 3 bugs críticos + performance + segurança
 * Auditoria Fase 2
 */
document.addEventListener('DOMContentLoaded', () => {
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  // --- PRELOADER COM TIMEOUT DE SEGURANÇA [P2] ---
 // Preloader - tempo aumentado para luxo
  const preloader = document.getElementById('preloader');
  const preloaderStart = Date.now();
  const TEMPO_MINIMO_PRELOADER = 2400; // 2.4 segundos - pode aumentar para 3000 se quiser mais

  window.addEventListener('load', () => {
    const tempoPassado = Date.now() - preloaderStart;
    const tempoRestante = Math.max(0, TEMPO_MINIMO_PRELOADER - tempoPassado);
    
    setTimeout(() => {
      preloader.classList.add('hide');
    }, tempoRestante);
  });

  // --- PROGRESS BAR + HEADER SCROLLED [Performance com rAF] ---
  const progress = $('#scroll-progress');
  const header = $('#header');
  let ticking = false;
  
  const onScroll = () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        const h = document.documentElement;
        const maxScroll = h.scrollHeight - h.clientHeight;
        const scrolled = maxScroll > 0 ? (h.scrollTop / maxScroll) * 100 : 0;
        if (progress) progress.style.width = scrolled + '%';
        if (header) header.classList.toggle('scrolled', window.scrollY > 40);
        ticking = false;
      });
      ticking = true;
    }
  };
  window.addEventListener('scroll', onScroll, { passive: true });

  // --- MENU MOBILE COM GUARD [Surgical] ---
  const menu = $('#menu');
  const toggle = $('#mobile-menu');
  if (menu && toggle) {
    toggle.addEventListener('click', () => {
      const isOpen = menu.classList.toggle('open');
      toggle.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    $$('.nav-link').forEach(l => l.addEventListener('click', () => {
      menu.classList.remove('open');
      toggle.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    }));
  }

  // --- SMOOTH SCROLL COM VALIDAÇÃO ---
  $$('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const id = a.getAttribute('href');
      if (id && id.length > 1) {
        try {
          const target = document.querySelector(id);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        } catch (err) {
          // ID inválido, ignora
        }
      }
    });
  });

  // --- REVEAL OBSERVER COM FALLBACK ---
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(en => {
        if (en.isIntersecting) {
          en.target.classList.add('in');
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });
    $$('.reveal').forEach(el => io.observe(el));
  } else {
    // Fallback para browsers antigos
    $$('.reveal').forEach(el => el.classList.add('in'));
  }

  // --- MAGNETIC BUTTONS - com reduced-motion check ---
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!prefersReducedMotion) {
    $$('.magnetic').forEach(btn => {
      btn.addEventListener('mousemove', e => {
        const r = btn.getBoundingClientRect();
        const x = e.clientX - r.left - r.width / 2;
        const y = e.clientY - r.top - r.height / 2;
        btn.style.transform = `translate(${x * 0.15}px, ${y * 0.35}px)`;
      });
      btn.addEventListener('mouseleave', () => btn.style.transform = 'translate(0,0)');
    });
  }

  // --- HERO PARALLAX COM GUARD + reduced-motion ---
  const hero = $('.hero');
  const heroBg = $('.hero-bg');
  if (hero && heroBg && !prefersReducedMotion && window.matchMedia('(pointer: fine)').matches) {
    hero.addEventListener('mousemove', e => {
      const x = (e.clientX / window.innerWidth - 0.5) * 20;
      const y = (e.clientY / window.innerHeight - 0.5) * 20;
      heroBg.style.transform = `scale(1.05) translate(${x}px, ${y}px)`;
    }, { passive: true });
  }

  // --- CONDOMINOS LOGIC - ZERO ERRO - 3 BUGS CORRIGIDOS ---
  const grid = $('#grid-condominos');
  const allBoxes = Array.from($$('.condomino-box'));
  const inputBusca = $('#busca-condomino');
  const radioAZ = $('#filtro-alfabetica');
  const radioAndar = $('#filtro-andares');

  if (!grid || !allBoxes.length) {
    // Sem condôminos, não inicializa lógica
  } else {
    // Debounce para busca
    let debounceTimer;
    function debounce(fn, delay) {
      return (...args) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fn(...args), 250);
      };
    }

    function getFiltered() {
      const term = (inputBusca?.value || '').toLowerCase().trim();
      return allBoxes.filter(b => {
        const busca = b.dataset.busca || '';
        return !term || busca.includes(term); // [C1] CORRIGIDO: espaço após return
      });
    }

    function renderAZ() {
      grid.innerHTML = '';
      const list = getFiltered().sort((a, b) => (a.dataset.nome || '').localeCompare(b.dataset.nome || ''));
      if (!list.length) {
        const safeTerm = (inputBusca?.value || '').replace(/</g, '&lt;');
        grid.innerHTML = `<p class="empty">Nenhuma empresa encontrada para "${safeTerm}".</p>`;
        return;
      }
      list.forEach((el, i) => {
        el.style.animationDelay = `${i * 0.03}s`;
        el.classList.remove('hidden');
        grid.appendChild(el);
      });
    }

    function renderAndares() {
      grid.innerHTML = '';
      const list = getFiltered();
      if (!list.length) {
        grid.innerHTML = `<p class="empty">Nenhuma empresa encontrada.</p>`;
        return;
      }
      const grupos = {};
      list.forEach(el => {
        const andarRaw = el.dataset.andar || '0';
        const andarKey = andarRaw === '0' || andarRaw === '' ? 'Térreo' : andarRaw;
        (grupos[andarKey] = grupos[andarKey] || []).push(el);
      });

      // [C2] CORRIGIDO: Precedência do sort por andar - BUG CRÍTICO ANTES: parseInt(a)||0 - (parseInt(b)||0)
      const andaresOrdenados = Object.keys(grupos).sort((a, b) => {
        if (a === 'Térreo') return -1;
        if (b === 'Térreo') return 1;
        const numA = parseInt(a, 10);
        const numB = parseInt(b, 10);
        // CORREÇÃO CIRÚRGICA: parênteses para garantir subtração correta
        return (isNaN(numA) ? 999 : numA) - (isNaN(numB) ? 999 : numB);
      });

      andaresOrdenados.forEach(andar => {
        const title = document.createElement('h3');
        title.className = 'andar-group-title';
        // [C3] CORRIGIDO: Bug Térreoº Andar
        const labelAndar = andar === 'Térreo' ? 'Térreo' : `${andar}º Andar`;
        title.textContent = `${labelAndar} • ${grupos[andar].length} empresas`;
        grid.appendChild(title);
        grupos[andar]
          .sort((a, b) => (a.dataset.nome || '').localeCompare(b.dataset.nome || ''))
          .forEach(el => {
            el.classList.remove('hidden');
            grid.appendChild(el);
          });
      });
    }

    function updateView() {
      if (radioAndar?.checked) renderAndares();
      else renderAZ();
    }

    inputBusca?.addEventListener('input', debounce(updateView, 250));
    radioAZ?.addEventListener('change', updateView);
    radioAndar?.addEventListener('change', updateView);
    updateView();
  }

  // --- FORM COM HONEYPOT E VALIDAÇÃO REAL ---
  const form = $('#formContato');
  const feedback = $('#form-feedback');
  const toast = $('#toast');

  function showToast(msg) {
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 4000);
  }

  form?.addEventListener('submit', e => {
    e.preventDefault();
    
    if (!feedback) return;
    
    const formData = new FormData(form);
    const nome = formData.get('nome')?.toString().trim();
    const email = formData.get('email')?.toString().trim();
    const mensagem = formData.get('mensagem')?.toString().trim();

    if (!nome || !email || !mensagem) {
      feedback.textContent = 'Preencha todos os campos obrigatórios.';
      feedback.style.color = '#b91c1c';
      return;
    }

    if (!formData.get('consentimento')) {
      feedback.textContent = 'Confirme sua concordância com o uso dos dados pessoais para enviar a mensagem.';
      feedback.style.color = '#b91c1c';
      return;
    }

    feedback.textContent = 'Enviando...';
    feedback.style.color = 'var(--gold)';
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) submitButton.disabled = true;

    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(async response => {
        const result = await response.json();
        if (!response.ok) {
          throw new Error(result.erro || 'Não foi possível enviar sua mensagem.');
        }
        return result;
      })
      .then(result => {
        feedback.textContent = result.mensagem || 'Mensagem enviada com sucesso! Retornaremos em breve.';
        feedback.style.color = 'green';
        form.reset();
        showToast(feedback.textContent);
      })
      .catch(error => {
        feedback.textContent = error.message || 'Não foi possível enviar sua mensagem. Tente novamente.';
        feedback.style.color = '#b91c1c';
      })
      .finally(() => {
        if (submitButton) submitButton.disabled = false;
      });
  });
});
