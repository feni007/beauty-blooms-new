<!doctype html>
<html lang="en" data-theme="light">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Beauty &amp; Blooms</title>
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
    <script>
      /* Apply saved or system theme before first paint to avoid a flash */
      try {
        var t =
          localStorage.getItem("bb-theme") ||
          (matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light");
        document.documentElement.dataset.theme = t;
      } catch (e) {
        console.warn("Theme preference is unavailable in this browser.", e);
      }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap"
      rel="stylesheet"
    />
  </head>
  <body class="loading">
    <!-- Loading screen -->
    <div class="loader" id="loader" aria-hidden="true">
      <div class="ld-center">
        <div class="ld-logo">
          <span class="w1">Beauty</span><span class="w2">&amp;</span
          ><span class="w3">Blooms</span>
        </div>
        <p class="ld-tag">Botanical beauty</p>
      </div>
      <div class="ld-bar"><i></i></div>
    </div>

    <nav class="nav" id="nav">
      <div class="container nav-in">
        <a href="#/" class="logo">Beauty <em>&amp;</em> Blooms</a>
        <div class="nav-links">
          <a href="#/shop" data-nav="shop">Shop all</a>
          @foreach ($storeCategories as $category)
            <a href="#/shop/{{ rawurlencode($category['name']) }}" data-nav="{{ $category['name'] }}">{{ $category['name'] }}</a>
          @endforeach
        </div>
        <div class="nav-right">
          <button
            class="icon-btn"
            id="themeBtn"
            aria-label="Switch between day and night mode"
          >
            <svg
              class="ic-moon"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.6"
              stroke-linecap="round"
            >
              <path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z" />
            </svg>
            <svg
              class="ic-sun"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.6"
              stroke-linecap="round"
            >
              <circle cx="12" cy="12" r="4" />
              <path
                d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"
              />
            </svg>
          </button>
          <button class="bag" id="bagBtn" aria-label="Open bag">
            Bag <span class="count" id="count">0</span>
          </button>
          @auth
            <a class="account-link" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('orders.index') }}">{{ auth()->user()->isAdmin() ? 'Admin' : 'My orders' }}</a>
            <form method="post" action="{{ route('logout') }}" class="nav-auth">@csrf<button class="account-link" type="submit">Sign out</button></form>
          @else
            <a class="account-link" href="{{ route('login') }}">Sign in</a>
          @endauth
          <button class="menu-btn" id="menuBtn" aria-label="Menu">
            <span></span><span></span>
          </button>
        </div>
      </div>
    </nav>

    <div class="sheet" id="sheet">
      <a href="#/shop">Shop all</a>
      @foreach ($storeCategories as $category)
        <a href="#/shop/{{ rawurlencode($category['name']) }}">{{ $category['name'] }}</a>
      @endforeach
      @auth
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('orders.index') }}">My account</a>
      @else
        <a href="{{ route('login') }}">Sign in / Register</a>
      @endauth
    </div>

    <main id="app"></main>

    <footer>
      <div class="container">
        <div class="foot-grid">
          <div>
            <a href="#/" class="logo">Beauty <em>&amp;</em> Blooms</a>
            <p style="margin-top: 20px; max-width: 330px; line-height: 1.65">
              Botanical skincare, considered colour and signature scent. Made in
              small batches, designed to be kept.
            </p>
            <div class="socials">
              <a href="#/" aria-label="Instagram">IG</a>
              <a href="#/" aria-label="Pinterest">PN</a>
              <a href="#/" aria-label="YouTube">YT</a>
            </div>
          </div>
          <div>
            <h5>Shop</h5>
            <ul>
              @foreach ($storeCategories as $category)
                <li><a href="#/shop/{{ rawurlencode($category['name']) }}">{{ $category['name'] }}</a></li>
              @endforeach
            </ul>
          </div>
          <div>
            <h5>Company</h5>
            <ul>
              <li><a href="#/">Our story</a></li>
              <li><a href="#/">Sustainability</a></li>
              <li><a href="#/">Journal</a></li>
              <li><a href="#/">Contact</a></li>
            </ul>
          </div>
          <div>
            <h5>Newsletter</h5>
            <p style="font-size: 14px; line-height: 1.6">
              New releases and private previews, twice a month.
            </p>
            <form
              class="news"
              id="footForm"
              style="
                display: flex;
                align-items: center;
                margin-top: 14px;
                border: 1px solid rgba(255, 255, 255, 0.14);
                border-radius: 999px;
                padding: 5px 5px 5px 20px;
              "
            >
              <input
                type="email"
                placeholder="Email address"
                required
                aria-label="Email address"
                style="
                  flex: 1;
                  min-width: 0;
                  background: none;
                  border: 0;
                  outline: none;
                  color: #fff;
                "
              />
              <button
                type="submit"
                style="
                  height: 40px;
                  padding: 0 18px;
                  border-radius: 999px;
                  background: #f5efe8;
                  color: #15110f;
                  font-weight: 500;
                  font-size: 13px;
                "
              >
                Subscribe
              </button>
            </form>
          </div>
        </div>
        <div class="foot-bottom">
          <span>© 2025 Beauty &amp; Blooms</span>
          <span
            ><a href="#/">Privacy</a> &nbsp;·&nbsp;
            <a href="#/">Terms</a> &nbsp;·&nbsp;
            <a href="#/">Accessibility</a></span
          >
        </div>
      </div>
    </footer>

    <div class="overlay" id="overlay"></div>
    <aside class="drawer" id="drawer" aria-label="Shopping bag">
      <div class="dh">
        <h3>Your bag</h3>
        <button class="x" id="closeBag" aria-label="Close">✕</button>
      </div>
      <div class="di" id="items"></div>
      <div class="df">
        <div class="ship">
          <div class="ship-bar"><i id="shipFill"></i></div>
          <p id="shipMsg"></p>
        </div>
        <div class="sub"><span>Subtotal</span><b id="total">$0</b></div>
        <small>Taxes calculated at checkout.</small>
        <button class="btn btn-ink" id="checkout">Checkout</button>
      </div>
    </aside>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>

    <script>
      /* ================= DATA ================= */
      const CATEGORIES = @js($storeCategories);
      const PRODUCTS = @js($storeProducts);

      /* ================= HELPERS ================= */
      const $ = (s, r = document) => r.querySelector(s);
      const $$ = (s, r = document) => [...r.querySelectorAll(s)];
      const byId = (id) => PRODUCTS.find((p) => p.id === Number(id));
      const money = (n) => "$" + n;
      const fallbackImage = @js(asset('images/product-placeholder.svg'));
      document.addEventListener("error", (event) => {
        const image = event.target;
        if (image instanceof HTMLImageElement && !image.dataset.fallbackApplied) {
          image.dataset.fallbackApplied = "true";
          image.src = fallbackImage;
        }
      }, true);
      const esc = (value) => String(value ?? "").replace(/[&<>"']/g, (char) => ({
        "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;",
      })[char]);
      const app = $("#app");
      const FREE_SHIP = 80;
      let page = "home";

      const media = (p) => `<div class="media" style="--tint:${p.tint}">
  ${p.tag ? `<span class="tag">${esc(p.tag)}</span>` : ""}
  <span class="ph">${esc(p.name)}</span>
  <img src="${esc(p.img)}" alt="${esc(p.name)}" loading="lazy"></div>`;
      const stars = (r) => {
        const f = Math.round(r);
        return `<span class="stars">${"★".repeat(f)}<span>${"★".repeat(5 - f)}</span></span>`;
      };
      function card(p, d = 0) {
        return `<article class="card rv" style="--d:${d}s">
    <a class="card-link" href="#/product/${p.id}" aria-label="${esc(p.name)}">
      ${media(p)}
      <div class="card-info">
        <div><h3>${esc(p.name)}</h3><p>${esc(p.cat)}</p></div>
        <span class="price">${money(p.price)}</span>
      </div>
    </a>
    <button class="add" data-add="${p.id}" ${p.stock < 1 ? "disabled" : ""}>${p.stock < 1 ? "Sold out" : "Add to bag"}</button>
  </article>`;
      }

      /* ================= PAGES ================= */
      function home() {
        const best = PRODUCTS.filter((p) => p.featured).slice(0, 4);
        const heroProduct = best[0] || PRODUCTS[0];
        app.innerHTML = `
  <div class="page">
    <section class="hero" id="hero">
      <div class="container hero-grid">
        <div>
          <p class="kicker rv">The Spring Edit</p>
          <h1><span class="ln"><span>Beauty,</span></span><span class="ln"><span>in full <em>bloom.</em></span></span></h1>
          <p class="hero-sub rv" style="--d:.5s">Botanical skincare, considered colour and signature scent, composed to make every ritual feel unhurried.</p>
          <div class="hero-cta rv" style="--d:.65s">
            <a class="btn btn-light magnetic" href="#/shop">Shop the collection</a>
            <a class="link" href="#/shop/Skincare">Explore skincare →</a>
          </div>
        </div>
        <div class="hero-media">
          <img id="heroImg" src="${heroProduct ? esc(heroProduct.img) : fallbackImage}" alt="Beauty & Blooms serum">
          <a class="glass" href="#/product/${heroProduct ? heroProduct.id : ''}">
            <div><strong>${heroProduct ? esc(heroProduct.name) : 'Discover the collection'}</strong><span>${heroProduct ? `${esc(heroProduct.tag || 'Featured')} · ${heroProduct.rating} rating` : 'Explore our products'}</span></div>
            <b>${heroProduct ? money(heroProduct.price) : ''}</b>
          </a>
        </div>
      </div>
      <div class="scroll-cue"></div>
    </section>

    <div class="marquee" aria-hidden="true"><div class="track">
      ${Array(2).fill(`<span>Cruelty-free</span><span>Refillable glass</span><span>Dermatologist tested</span><span>Vegan formulas</span><span>Complimentary delivery over $80</span>`).join("")}
    </div></div>

    <section class="section">
      <div class="container">
        <div class="head">
          <div>
            <p class="kicker rv">Categories</p>
            <h2 class="h2 rv" style="--d:.08s">Find your ritual.</h2>
          </div>
          <p class="lead rv" style="--d:.16s;margin-top:0">${CATEGORIES.length} collections, each designed to pair with the others.</p>
        </div>
        <div class="cats">
          ${CATEGORIES.map(
            (c, i) => `
          <a class="cat rv" style="--d:${i * 0.08}s" href="#/shop/${encodeURIComponent(c.name)}">
            <img src="${esc(c.img)}" alt="${esc(c.name)}" loading="lazy" onerror="this.style.opacity=0">
            <div class="cat-body">
              <div><h3>${esc(c.name)}</h3><p>${esc(c.desc)}</p></div>
              <span class="arrow" aria-hidden="true">→</span>
            </div>
          </a>`,
          ).join("")}
        </div>
      </div>
    </section>

    <section class="band">
      <div class="band-img" data-parallax><img src="{{ asset('images/product-placeholder.svg') }}" alt="" onerror="this.style.opacity=0"></div>
      <div class="container band-txt">
        <p class="kicker rv">Signature</p>
        <h2 class="rv" style="--d:.08s">The rose, <em>distilled.</em></h2>
        <p class="rv" style="--d:.16s">Damask rose water and hyaluronic acid, balanced into a serum that feels like a cool breath on warm skin.</p>
        <a class="btn btn-light magnetic rv" style="--d:.24s" href="#/product/${heroProduct ? heroProduct.id : ''}">Discover ${heroProduct ? esc(heroProduct.name) : 'the collection'}</a>
      </div>
      <div class="band-meta">
        <div><b>24h</b>hydration</div>
        <div><b>98%</b>naturally derived</div>
        <div><b>4.9</b>average rating</div>
      </div>
    </section>

    <section class="section" style="padding-top:150px">
      <div class="container">
        <div class="head">
          <div>
            <p class="kicker rv">Bestsellers</p>
            <h2 class="h2 rv" style="--d:.08s">Loved, every day.</h2>
          </div>
          <a class="link rv" href="#/shop">View all products →</a>
        </div>
        <div class="grid">${best.map((p, i) => card(p, i * 0.1)).join("")}</div>
      </div>
    </section>

    <section class="section" style="padding-top:0">
      <div class="container split">
        <div class="split-img rv"><img src="{{ asset('images/product-placeholder.svg') }}" alt="" onerror="this.style.opacity=0"></div>
        <div>
          <p class="kicker rv">Our standard</p>
          <h2 class="h2 rv" style="--d:.08s">Made in small batches. <em>Kept for years.</em></h2>
          <p class="lead rv" style="--d:.16s">Every formula is developed with responsibly sourced botanicals, then tested for performance and comfort before it reaches your hands.</p>
          <div class="stats">
            <div class="stat rv" style="--d:.2s"><b class="count-up" data-to="100">0</b><span>% cruelty-free, certified</span></div>
            <div class="stat rv" style="--d:.3s"><b class="count-up" data-to="98">0</b><span>% naturally derived ingredients</span></div>
            <div class="stat rv" style="--d:.4s"><b class="count-up" data-to="12">0</b><span>refills per bottle, on average</span></div>
          </div>
        </div>
      </div>
    </section>

    <section class="section deep">
      <div class="container">
        <div class="head">
          <div>
            <p class="kicker rv">The ritual</p>
            <h2 class="h2 rv" style="--d:.08s">Three steps. <em>One glow.</em></h2>
          </div>
        </div>
        <div class="steps">
          <div class="step rv" style="--d:.05s"><div class="n">i.</div><h4>Cleanse and prepare</h4><p>Begin with clean skin so every product can absorb fully and work as intended.</p></div>
          <div class="step rv" style="--d:.15s"><div class="n">ii.</div><h4>Hydrate and treat</h4><p>Press in a serum, then seal with a moisturiser that suits your skin, morning and night.</p></div>
          <div class="step rv" style="--d:.25s"><div class="n">iii.</div><h4>Finish with scent</h4><p>A single spritz on pulse points completes the ritual and lingers through the day.</p></div>
        </div>
      </div>
    </section>

    <section class="section" style="padding-top:0">
      <div class="container">
        <div class="head">
          <div>
            <p class="kicker rv">Journal</p>
            <h2 class="h2 rv" style="--d:.08s">Notes on the ritual.</h2>
          </div>
          <a class="link rv" href="#/">All stories →</a>
        </div>
        <div class="journal">
          <a class="jcard rv" href="#/shop/Skincare" style="--d:0s">
            <div class="jimg"><img src="{{ asset('images/product-placeholder.svg') }}" alt="" loading="lazy" onerror="this.style.opacity=0"></div>
            <p class="kicker">Skincare · 4 min</p>
            <h3>Why we layer, and in what order</h3>
            <p>A simple guide to building a routine where every step does its job.</p>
          </a>
          <a class="jcard rv" href="#/shop/Makeup" style="--d:.1s">
            <div class="jimg"><img src="{{ asset('images/product-placeholder.svg') }}" alt="" loading="lazy" onerror="this.style.opacity=0"></div>
            <p class="kicker">Makeup · 3 min</p>
            <h3>The art of the soft flush</h3>
            <p>How to blend blush so it looks like you, only a little brighter.</p>
          </a>
          <a class="jcard rv" href="#/shop/Fragrance" style="--d:.2s">
            <div class="jimg"><img src="{{ asset('images/product-placeholder.svg') }}" alt="" loading="lazy" onerror="this.style.opacity=0"></div>
            <p class="kicker">Fragrance · 5 min</p>
            <h3>Layering scent like a perfumer</h3>
            <p>Why a little less, applied in the right places, goes much further.</p>
          </a>
        </div>
      </div>
    </section>

    <section class="section" style="padding-top:0">
      <div class="container">
        <div class="cta rv">
          <div>
            <h2>Join the <em>garden.</em></h2>
            <p>Private previews, early access to new releases, and a considered note from our perfumers and chemists twice a month.</p>
          </div>
          <div>
            <form id="ctaForm">
              <input type="email" placeholder="Email address" required aria-label="Email address">
              <button class="btn btn-ink" type="submit">Subscribe</button>
            </form>
            <small>No noise. Unsubscribe at any time.</small>
          </div>
        </div>
      </div>
    </section>
  </div>`;
        bindMagnetic();
        $("#ctaForm").addEventListener("submit", (e) => {
          e.preventDefault();
          toast("You are subscribed");
          e.target.reset();
        });
      }

      function shop(cat) {
        const active = CATEGORIES.some((c) => c.name === cat) ? cat : "All";
        const names = ["All", ...CATEGORIES.map((c) => c.name)];
        const title = (a) =>
          a === "All"           ? "All <em>products.</em>" : `${esc(a)} <em>edit.</em>`;
        const desc = (a) =>
          a === "All"
            ? "Skincare, colour, fragrance, hair and body, selected for quality and restraint."
            : esc(CATEGORIES.find((c) => c.name === a).desc);
        const countOf = (n) =>
          n === "All"
            ? PRODUCTS.length
            : PRODUCTS.filter((p) => p.cat === n).length;

        app.innerHTML = `
  <div class="page">
    <div class="container">
      <header class="shop-head">
        <p class="kicker">Shop</p>
        <h1 id="stitle">${title(active)}</h1>
        <p class="lead" id="sdesc">${desc(active)}</p>
      </header>
      <div class="toolbar">
        <div class="pills" id="pills">
          ${names.map((n) => `<button class="pill ${n === active ? "on" : ""}" data-pill="${esc(n)}">${esc(n)}<small>${countOf(n)}</small></button>`).join("")}
        </div>
        <div class="tools">
          <input class="field" id="q" type="search" placeholder="Search" aria-label="Search products">
          <select class="field" id="sort" aria-label="Sort products">
            <option value="featured">Featured</option>
            <option value="low">Price, low to high</option>
            <option value="high">Price, high to low</option>
            <option value="rating">Top rated</option>
          </select>
        </div>
      </div>
    </div>
    <section class="container shop-body"><div class="grid" id="grid"></div></section>
  </div>`;

        let cur = active,
          q = "",
          sort = "featured";
        const grid = $("#grid");

        const draw = () => {
          let list = PRODUCTS.filter(
            (p) =>
              (cur === "All" || p.cat === cur) &&
              (p.name + " " + p.cat)
                .toLowerCase()
                .includes(q.trim().toLowerCase()),
          );
          if (sort === "low") list.sort((a, b) => a.price - b.price);
          if (sort === "high") list.sort((a, b) => b.price - a.price);
          if (sort === "rating") list.sort((a, b) => b.rating - a.rating);
          grid.innerHTML = list.length
            ? list.map((p, i) => card(p, i * 0.05)).join("")
            : `<div class="empty"><h3>Nothing matches.</h3><p>Try another search or category.</p></div>`;
          observe();
        };

        $("#pills").addEventListener("click", (e) => {
          const b = e.target.closest("[data-pill]");
          if (!b) return;
          cur = b.dataset.pill;
          $$(".pill").forEach((x) => x.classList.toggle("on", x === b));
          $("#stitle").innerHTML = title(cur);
          $("#sdesc").textContent = desc(cur);
          $$("[data-nav]").forEach((l) =>
            l.classList.toggle(
              "active",
              l.dataset.nav === (cur === "All" ? "shop" : cur),
            ),
          );
          history.replaceState(
            null,
            "",
            cur === "All" ? "#/shop" : `#/shop/${encodeURIComponent(cur)}`,
          );
          draw();
        });
        $("#q").addEventListener("input", (e) => {
          q = e.target.value;
          draw();
        });
        $("#sort").addEventListener("change", (e) => {
          sort = e.target.value;
          draw();
        });
        draw();
      }

      function product(id) {
        const p = byId(id);
        if (!p) {
          location.hash = "#/shop";
          return;
        }
        const related = [
          ...PRODUCTS.filter((x) => x.id !== p.id && x.cat === p.cat),
          ...PRODUCTS.filter((x) => x.id !== p.id && x.cat !== p.cat),
        ].slice(0, 4);
        let opt = p.opts[0],
          qty = 1;

        app.innerHTML = `
  <div class="page">
    <div class="container">
      <nav class="crumbs" aria-label="Breadcrumb"><a href="#/">Home</a><span>/</span><a href="#/shop/${encodeURIComponent(p.cat)}">${esc(p.cat)}</a><span>/</span><span class="here">${esc(p.name)}</span></nav>
      <div class="pdp">
        <div class="gallery">
          <div class="zoom rv" id="zoom" style="--tint:${p.tint}" role="button" tabindex="0" aria-pressed="false" aria-label="Zoom product image">
            <div class="zoom-inner">
              <span class="ph">${esc(p.name)}</span>
              <img src="${esc(p.img)}" alt="${esc(p.name)}">
            </div>
            <div class="zoom-hint" id="zoomHint">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M11 8v6M8 11h6"/></svg>
              <span>Click to zoom in</span>
            </div>
          </div>
        </div>
        <div class="info">
          <p class="kicker rv">${esc(p.cat)}</p>
          <h1 class="rv" style="--d:.06s">${esc(p.name)}</h1>
          <div class="rating rv" style="--d:.12s">${stars(p.rating)}<span>${p.rating} · ${p.reviews.toLocaleString()} reviews</span></div>
          <div class="price-big rv" style="--d:.16s">${money(p.price)}</div>
          <p class="short rv" style="--d:.2s">${esc(p.short)}</p>

          <div class="opt-label rv" style="--d:.24s">${esc(p.optLabel)} <span id="optName">${esc(opt)}</span></div>
          <div class="opts rv" id="opts" style="--d:.26s">
            ${p.opts.map((o, i) => `<button class="opt ${i === 0 ? "on" : ""}" data-opt="${esc(o)}">${p.swatches?.[i] && /^#[0-9A-Fa-f]{6}$/.test(p.swatches[i]) ? `<i style="background:${p.swatches[i]}"></i>` : ""}${esc(o)}</button>`).join("")}
          </div>

          <div class="buy rv" style="--d:.3s">
            <div class="stepper">
              <button id="minus" aria-label="Decrease quantity">−</button>
              <b id="qty">1</b>
              <button id="plus" aria-label="Increase quantity">+</button>
            </div>
            <button class="btn btn-ink magnetic" id="addBtn" ${p.stock < 1 ? "disabled" : ""}>${p.stock < 1 ? "Sold out" : `Add to bag · ${money(p.price)}`}</button>
          </div>

          <div class="perks rv" style="--d:.34s">
            <div class="perk"><b>Complimentary delivery</b>On orders over $80</div>
            <div class="perk"><b>30-day returns</b>Simple and unquestioned</div>
            <div class="perk"><b>Gift wrapping</b>Available at checkout</div>
            <div class="perk"><b>Sample included</b>With every order</div>
          </div>

          <div class="acc rv" style="--d:.38s">
            <details open><summary>Description</summary><div class="body">${esc(p.desc)}</div></details>
            <details><summary>Benefits</summary><div class="body"><ul>${p.benefits.map((b) => `<li>${esc(b)}</li>`).join("")}</ul></div></details>
            <details><summary>Ingredients</summary><div class="body">${esc(p.ingredients)}</div></details>
            <details><summary>How to use</summary><div class="body">${esc(p.how)}</div></details>
          </div>
        </div>
      </div>
    </div>

    <section class="related">
      <div class="container">
        <div class="head">
          <div>
            <p class="kicker rv">Complete the ritual</p>
            <h2 class="h2 rv" style="--d:.08s">You may also like.</h2>
          </div>
          <a class="link rv" href="#/shop/${encodeURIComponent(p.cat)}">More ${esc(p.cat)} →</a>
        </div>
        <div class="grid">${related.map((r, i) => card(r, i * 0.08)).join("")}</div>
      </div>
    </section>
  </div>`;

        $("#opts").addEventListener("click", (e) => {
          const b = e.target.closest("[data-opt]");
          if (!b) return;
          opt = b.dataset.opt;
          $$("#opts .opt").forEach((x) => x.classList.toggle("on", x === b));
          $("#optName").textContent = opt;
        });
        $("#minus").onclick = () => {
          qty = Math.max(1, qty - 1);
          $("#qty").textContent = qty;
        };
        $("#plus").onclick = () => {
          qty = Math.min(10, p.stock, qty + 1);
          $("#qty").textContent = qty;
        };
        $("#addBtn").onclick = (e) => {
          addToCart(p.id, opt, qty);
          const b = e.currentTarget;
          b.classList.add("added");
          b.textContent = "Added to bag ✓";
          setTimeout(() => {
            b.classList.remove("added");
            b.textContent = `Add to bag · ${money(p.price)}`;
          }, 1800);
        };

        initZoom();
        bindMagnetic();
      }

      /* ================= ZOOM (click to zoom in / out) ================= */
      function initZoom() {
        const z = $("#zoom");
        if (!z) return;
        const inner = z.querySelector(".zoom-inner");
        const hint = $("#zoomHint span");
        const S = 2.2;
        let on = false;

        const place = (cx, cy) => {
          const r = z.getBoundingClientRect();
          const px = (cx - r.left) / r.width - 0.5;
          const py = (cy - r.top) / r.height - 0.5;
          inner.style.setProperty("--tx", -px * r.width * (S - 1) + "px");
          inner.style.setProperty("--ty", -py * r.height * (S - 1) + "px");
        };
        const setZoom = (state, e) => {
          on = state;
          z.classList.toggle("is-zoomed", on);
          z.setAttribute("aria-pressed", on);
          hint.textContent = on ? "Click to zoom out" : "Click to zoom in";
          if (on && e && e.clientX) place(e.clientX, e.clientY);
          if (!on) {
            inner.style.setProperty("--tx", "0px");
            inner.style.setProperty("--ty", "0px");
          }
        };

        z.addEventListener("click", (e) => setZoom(!on, e));
        z.addEventListener("keydown", (e) => {
          if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            setZoom(!on, null);
          }
        });
        z.addEventListener("pointermove", (e) => {
          if (on && e.pointerType !== "touch") place(e.clientX, e.clientY);
        });
        z.addEventListener(
          "touchmove",
          (e) => {
            if (!on) return;
            const t = e.touches[0];
            place(t.clientX, t.clientY);
          },
          { passive: true },
        );
        document.addEventListener("keydown", (e) => {
          if (e.key === "Escape" && on) setZoom(false);
        });
      }

      /* ================= MOTION ================= */
      function onScroll() {
        const y = window.scrollY;
        $("#nav").classList.toggle("solid", page !== "home" || y > 40);
        const img = $("#heroImg");
        if (img && y < innerHeight)
          img.style.transform = `scale(1.08) translate3d(0,${Math.min(y, 800) * 0.14}px,0)`;
        $$("[data-parallax]").forEach((el) => {
          const r = el.parentElement.getBoundingClientRect();
          if (r.bottom < 0 || r.top > innerHeight) return;
          const off = r.top + r.height / 2 - innerHeight / 2;
          el.style.transform = `translate3d(0,${off * -0.18}px,0)`;
        });
      }
      window.addEventListener("scroll", onScroll, { passive: true });

      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((en) => {
            if (!en.isIntersecting) return;
            en.target.classList.add("in");
            if (en.target.classList.contains("count-up")) countUp(en.target);
            io.unobserve(en.target);
          });
        },
        { threshold: 0.15, rootMargin: "0px 0px -60px 0px" },
      );
      function observe() {
        $$(".rv:not(.in), .count-up").forEach((el) => io.observe(el));
      }
      function countUp(el) {
        const to = +el.dataset.to,
          t0 = performance.now(),
          dur = 1800;
        const step = (now) => {
          const k = Math.min((now - t0) / dur, 1);
          el.textContent = Math.round(to * (1 - Math.pow(1 - k, 3)));
          if (k < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
      }
      function bindMagnetic() {
        $$(".magnetic").forEach((el) => {
          el.addEventListener("pointermove", (e) => {
            const r = el.getBoundingClientRect();
            const x = e.clientX - (r.left + r.width / 2);
            const y = e.clientY - (r.top + r.height / 2);
            el.style.transform = `translate(${x * 0.2}px, ${y * 0.3}px)`;
          });
          el.addEventListener("pointerleave", () => (el.style.transform = ""));
        });
      }

      /* ================= THEME (day / night) ================= */
      function setTheme(t) {
        document.documentElement.dataset.theme = t;
        try {
          localStorage.setItem("bb-theme", t);
        } catch (e) {
          console.warn("Theme preference could not be saved.", e);
        }
      }
      $("#themeBtn").addEventListener("click", (e) => {
        const next =
          document.documentElement.dataset.theme === "dark" ? "light" : "dark";
        const b = e.currentTarget.getBoundingClientRect();
        const x = e.clientX || b.left + b.width / 2;
        const y = e.clientY || b.top + b.height / 2;
        const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;

        if (!document.startViewTransition || reduce) {
          setTheme(next);
          return;
        }

        const r = Math.hypot(
          Math.max(x, innerWidth - x),
          Math.max(y, innerHeight - y),
        );
        const vt = document.startViewTransition(() => setTheme(next));
        vt.ready.then(() => {
          document.documentElement.animate(
            {
              clipPath: [
                `circle(0px at ${x}px ${y}px)`,
                `circle(${r}px at ${x}px ${y}px)`,
              ],
            },
            {
              duration: 850,
              easing: "cubic-bezier(.16,1,.3,1)",
              pseudoElement: "::view-transition-new(root)",
            },
          );
        });
      });

      /* ================= MOBILE MENU ================= */
      function openSheet() {
        $("#sheet").classList.add("open");
        $("#menuBtn").classList.add("open");
        document.body.classList.add("menu-open");
      }
      function closeSheet() {
        $("#sheet").classList.remove("open");
        $("#menuBtn").classList.remove("open");
        document.body.classList.remove("menu-open");
      }
      $("#menuBtn").addEventListener("click", () =>
        $("#sheet").classList.contains("open") ? closeSheet() : openSheet(),
      );
      $$("#sheet a").forEach((a) => a.addEventListener("click", closeSheet));

      /* ================= CART ================= */
      const cart = (() => {
        try {
          const saved = JSON.parse(localStorage.getItem("bb-cart") || "[]");
          return Array.isArray(saved)
            ? saved.filter((item) => {
                const product = byId(item.id);
                return product && product.opts.includes(String(item.opt)) && Number.isInteger(item.qty) && item.qty > 0;
              }).map((item) => {
                const id = Number(item.id);
                const opt = String(item.opt);
                return { id, opt, qty: item.qty, key: `${id}|${opt}` };
              })
            : [];
        } catch (error) {
          console.error("Unable to read the saved shopping bag.", error);
          return [];
        }
      })();
      function persistCart() {
        try {
          localStorage.setItem("bb-cart", JSON.stringify(cart));
        } catch (error) {
          console.error("Unable to save the shopping bag.", error);
        }
      }
      function addToCart(id, opt, qty = 1) {
        const p = byId(id);
        if (!p || p.stock < 1) return toast("This product is currently out of stock.");
        opt = opt || p.opts[0];
        const key = `${id}|${opt}`;
        const alreadyInBag = cart.filter((item) => item.id === Number(id)).reduce((sum, item) => sum + item.qty, 0);
        const quantityToAdd = Math.min(qty, p.stock - alreadyInBag);
        if (quantityToAdd < 1) return toast("You have added all available stock to your bag.");
        const it = cart.find((i) => i.key === key);
        if (it) it.qty += quantityToAdd;
        else cart.push({ key, id: Number(id), opt, qty: quantityToAdd });
        renderCart();
        toast(`${p.name} added to bag`);
        const c = $("#count");
        c.classList.remove("bump");
        void c.offsetWidth;
        c.classList.add("bump");
      }
      function renderCart() {
        persistCart();
        const count = cart.reduce((n, i) => n + i.qty, 0);
        const total = cart.reduce((n, i) => n + byId(i.id).price * i.qty, 0);
        $("#count").textContent = count;
        $("#total").textContent = money(total);

        const pct = Math.min(total / FREE_SHIP, 1);
        $("#shipFill").style.transform = `scaleX(${pct})`;
        $("#shipMsg").textContent =
          total >= FREE_SHIP
            ? "You have unlocked complimentary delivery."
            : `Add ${money(FREE_SHIP - total)} for complimentary delivery.`;

        const list = $("#items");
        if (!cart.length) {
          list.innerHTML = `<div class="dempty"><h4>Your bag is empty.</h4><p>Explore the collection to begin.</p></div>`;
          return;
        }
        list.innerHTML = cart
          .map((i) => {
            const p = byId(i.id);
            return `<div class="item">
      <div class="thumb" style="--tint:${p.tint}"><img src="${esc(p.img)}" alt=""></div>
      <div>
        <strong>${esc(p.name)}</strong>
        <small>${esc(i.opt)}</small>
        <div class="stepper">
          <button data-act="-1" data-key="${esc(i.key)}" aria-label="Decrease">−</button>
          <b>${i.qty}</b>
          <button data-act="1" data-key="${esc(i.key)}" aria-label="Increase">+</button>
        </div>
      </div>
      <div class="item-r">
        <span>${money(p.price * i.qty)}</span>
        <button data-remove="${esc(i.key)}">Remove</button>
      </div>
    </div>`;
          })
          .join("");
      }
      $("#items").addEventListener("click", (e) => {
        const a = e.target.closest("[data-act]");
        const r = e.target.closest("[data-remove]");
        if (a) {
          const it = cart.find((i) => i.key === a.dataset.key);
          if (it) {
            const change = Number(a.dataset.act);
            if (change < 0) {
              it.qty += change;
              if (it.qty < 1) cart.splice(cart.indexOf(it), 1);
            } else {
              const product = byId(it.id);
              const otherQuantity = cart.filter((item) => item.id === it.id && item !== it).reduce((sum, item) => sum + item.qty, 0);
              if (it.qty < product.stock - otherQuantity) it.qty += 1;
              else toast("You have added all available stock to your bag.");
            }
          }
        }
        if (r) {
          const idx = cart.findIndex((i) => i.key === r.dataset.remove);
          if (idx > -1) cart.splice(idx, 1);
        }
        if (a || r) renderCart();
      });
      function openBag() {
        $("#drawer").classList.add("open");
        $("#overlay").classList.add("open");
      }
      function closeBag() {
        $("#drawer").classList.remove("open");
        $("#overlay").classList.remove("open");
      }
      $("#bagBtn").addEventListener("click", openBag);
      $("#closeBag").addEventListener("click", closeBag);
      $("#overlay").addEventListener("click", closeBag);
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeBag();
      });
      $("#checkout").addEventListener("click", () => {
        if (!cart.length) return toast("Your bag is empty");
        window.location.href = @js(route('checkout.create'));
      });
      document.addEventListener("click", (e) => {
        const add = e.target.closest("[data-add]");
        if (add) {
          e.preventDefault();
          addToCart(add.dataset.add);
        }
      });
      $("#footForm").addEventListener("submit", (e) => {
        e.preventDefault();
        toast("You are subscribed");
        e.target.reset();
      });

      /* ================= TOAST ================= */
      let tt;
      function toast(msg) {
        const t = $("#toast");
        t.innerHTML = `<i>✓</i>${esc(msg)}`;
        t.classList.add("show");
        clearTimeout(tt);
        tt = setTimeout(() => t.classList.remove("show"), 2400);
      }

      /* ================= ROUTER ================= */
      function route() {
        const [, a = "", b = ""] = location.hash.split("/").map((s) => {
          try {
            return decodeURIComponent(s);
          } catch (e) {
            return s;
          }
        });
        page = a || "home";
        closeSheet();
        closeBag();
        if (a === "shop") shop(b);
        else if (a === "product") product(b);
        else home();
        window.scrollTo({ top: 0, behavior: "instant" });
        $$("[data-nav]").forEach((l) =>
          l.classList.toggle(
            "active",
            a === "shop" && l.dataset.nav === (b || "shop"),
          ),
        );
        onScroll();
        observe();
      }
      window.addEventListener("hashchange", route);

      /* ================= LOADER ================= */
      (function boot() {
        const loader = $("#loader");
        const started = performance.now();
        let finished = false;
        const finish = () => {
          if (finished) return;
          finished = true;
          const wait = Math.max(0, 2400 - (performance.now() - started));
          setTimeout(() => {
            loader.classList.add("done");
            document.body.classList.remove("loading");
            setTimeout(() => loader.remove(), 1300);
          }, wait);
        };
        window.addEventListener("load", finish);
        setTimeout(finish, 4000); // never leave the visitor waiting on slow assets
      })();

      route();
      renderCart();
    </script>
  </body>
</html>