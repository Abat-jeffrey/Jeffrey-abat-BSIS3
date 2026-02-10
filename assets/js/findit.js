function initAutoRefresh(containerId, url, interval = 2500) {
    const el = document.getElementById(containerId);
    if (!el) return;

    const load = async () => {
        const resp = await fetch(url);
        const html = await resp.text();
        el.innerHTML = html;
        el.scrollTop = el.scrollHeight;
    };

    load();
    setInterval(load, interval);
}
