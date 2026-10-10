import Alpine from 'alpinejs';

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function api(url, { method = 'GET', body = null } = {}) {
    const res = await fetch(url, {
        method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf(),
            Accept: 'application/json',
            ...(body ? { 'Content-Type': 'application/json' } : {}),
        },
        body: body ? JSON.stringify(body) : null,
    });

    let data = {};
    try { data = await res.json(); } catch (e) { /* no-op */ }
    return { ok: res.ok, status: res.status, data };
}

/* ------------------------------------------------------------- WhatsApp */
// WhatsApp buttons carry the wa.me URL in data-wa-href instead of href, so the
// browser never shows the long URL in the status bar on hover.
document.addEventListener('click', (event) => {
    const el = event.target.closest('[data-wa-href]');
    if (!el) return;
    event.preventDefault();
    window.open(el.dataset.waHref, '_blank', 'noopener');
});

/* ---------------------------------------------------------------- Toasts */
window.vpToast = function (message, type = 'success') {
    window.dispatchEvent(new CustomEvent('vp-toast', { detail: { message, type } }));
};

Alpine.data('toastHub', () => ({
    toasts: [],
    add(detail) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, ...detail });
        setTimeout(() => this.dismiss(id), 4000);
    },
    dismiss(id) {
        this.toasts = this.toasts.filter((t) => t.id !== id);
    },
}));

/* ------------------------------------------------------------ Cart store */
Alpine.store('cart', {
    count: window.__gcCartCount ?? 0,
    open: false,
    loading: false,
    drawerHtml: '',
    setCount(n) { this.count = n; },
    async openDrawer() {
        this.open = true;
        this.loading = true;
        const res = await fetch('/cart/mini', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        this.drawerHtml = await res.text();
        this.loading = false;
    },
    close() { this.open = false; },
});

Alpine.data('addToCart', (productId, hasVariants = false) => ({
    qty: 1,
    variantId: null,
    busy: false,
    async submit() {
        if (hasVariants && !this.variantId) {
            window.vpToast('Please choose the product options first.', 'error');
            return;
        }
        this.busy = true;
        const { ok, data } = await api('/cart', {
            method: 'POST',
            body: { product_id: productId, variant_id: this.variantId, quantity: this.qty },
        });
        this.busy = false;
        window.vpToast(data.message ?? (ok ? 'Added to cart.' : 'Could not add to cart.'), ok ? 'success' : 'error');
        if (ok) {
            Alpine.store('cart').setCount(data.cart_count);
            Alpine.store('cart').openDrawer();
        }
    },
}));

Alpine.data('cartLine', (itemId) => ({
    busy: false,
    async update(qty) {
        if (qty < 1) return this.remove();
        this.busy = true;
        const { ok, data } = await api(`/cart/${itemId}`, { method: 'PATCH', body: { quantity: qty } });
        this.busy = false;
        if (ok) { Alpine.store('cart').setCount(data.cart_count); window.location.reload(); }
        else window.vpToast(data.message, 'error');
    },
    async remove() {
        this.busy = true;
        const { ok, data } = await api(`/cart/${itemId}`, { method: 'DELETE' });
        this.busy = false;
        if (ok) { Alpine.store('cart').setCount(data.cart_count); window.location.reload(); }
    },
}));

/* --------------------------------------------------------- Wishlist toggle */
Alpine.data('wishlistButton', (slug, initial = false) => ({
    inList: initial,
    busy: false,
    async toggle() {
        this.busy = true;
        const { ok, status, data } = await api(`/wishlist/${slug}`, { method: 'POST' });
        this.busy = false;
        if (status === 401) { window.location = data.login_url ?? '/login'; return; }
        if (ok) {
            this.inList = data.in_list;
            window.vpToast(data.message, 'success');
        }
    },
}));

/* ---------------------------------------------------- Search autocomplete */
Alpine.data('searchBox', () => ({
    q: '',
    results: { products: [], categories: [] },
    open: false,
    loading: false,
    timer: null,
    onInput() {
        clearTimeout(this.timer);
        if (this.q.trim().length < 2) { this.open = false; return; }
        this.timer = setTimeout(() => this.fetch(), 220);
    },
    async fetch() {
        this.loading = true;
        const res = await fetch(`/search/suggest?q=${encodeURIComponent(this.q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        this.results = await res.json();
        this.loading = false;
        this.open = true;
    },
    get hasResults() {
        return this.results.products.length || this.results.categories.length;
    },
}));

/* ------------------------------------------------------- Variant selector */
Alpine.data('variantPicker', (slug, config) => ({
    selected: {},
    variantId: null,
    price: config.price,
    inStock: config.inStock,
    stock: config.stock,
    image: config.image,
    async choose(attrId, valueId) {
        this.selected[attrId] = valueId;
        if (Object.keys(this.selected).length < config.attributeCount) return;
        const params = new URLSearchParams();
        Object.values(this.selected).forEach((v) => params.append('values[]', v));
        const res = await fetch(`/product/${slug}/variant?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();
        if (data.found) {
            this.variantId = data.variant_id;
            this.price = data.price_formatted;
            this.inStock = data.in_stock;
            this.stock = data.stock;
            if (data.image) this.image = data.image;
        } else {
            this.variantId = null;
            this.inStock = false;
        }
    },
    isSelected(attrId, valueId) {
        return this.selected[attrId] === valueId;
    },
}));

/* ----------------------------------------------------------- Quick view */
Alpine.data('quickView', () => ({
    open: false,
    loading: false,
    html: '',
    async show(slug) {
        this.open = true;
        this.loading = true;
        const res = await fetch(`/product/${slug}/quick-view`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        this.html = await res.text();
        this.loading = false;
    },
}));

/* ------------------------------------------------------- Back to top btn */
Alpine.data('backToTop', () => ({
    show: false,
    init() {
        window.addEventListener('scroll', () => { this.show = window.scrollY > 600; }, { passive: true });
    },
    up() { window.scrollTo({ top: 0, behavior: 'smooth' }); },
}));

/* ------------------------------------------------ Async form (newsletter) */
Alpine.data('asyncForm', () => ({
    busy: false,
    async submit(e) {
        this.busy = true;
        const form = e.target;
        const { ok, data } = await api(form.action, {
            method: 'POST',
            body: Object.fromEntries(new FormData(form)),
        });
        this.busy = false;
        window.vpToast(data.message ?? (ok ? 'Done.' : 'Something went wrong.'), ok ? 'success' : 'error');
        if (ok) form.reset();
    },
}));
