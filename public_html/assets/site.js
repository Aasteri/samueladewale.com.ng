(function () {
  var root = document.documentElement;
  var header = document.getElementById("site-header");
  if (window.matchMedia("(hover: hover)").matches) {
    var raf = 0, x = 0, y = 0;
    window.addEventListener("pointermove", function (e) {
      x = e.clientX; y = e.clientY;
      if (!raf) raf = requestAnimationFrame(function () {
        raf = 0;
        root.style.setProperty("--mouse-x", x + "px");
        root.style.setProperty("--mouse-y", y + "px");
      });
    }, { passive: true });
  }
  document.querySelectorAll(".spotlight-card").forEach(function (el) {
    el.addEventListener("mousemove", function (e) {
      var r = el.getBoundingClientRect();
      el.style.setProperty("--card-x", (e.clientX - r.left) + "px");
      el.style.setProperty("--card-y", (e.clientY - r.top) + "px");
    });
  });
  if (header) {
    var onScroll = function () {
      header.classList.toggle("border-b", window.scrollY > 8);
      header.classList.toggle("border-white/[0.06]", window.scrollY > 8);
      header.classList.toggle("bg-[#060912]/80", window.scrollY > 8);
      header.classList.toggle("backdrop-blur-md", window.scrollY > 8);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }
  var toggle = document.getElementById("menu-toggle");
  var nav = document.getElementById("mobile-nav");
  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("hidden") === false;
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    });
  }
  var form = document.getElementById("contact-form");
  if (!form) return;
  var status = document.getElementById("form-status");
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var btn = form.querySelector("button[type=submit] span");
    if (btn) btn.textContent = "Sending message…";
    status.textContent = "Delivering to my inbox. This can take a few seconds.";
    var trap = form.querySelector("[name=company_website], [name=website]");
    if (trap) trap.value = "";
    var data = { sent_from_page: "contact" };
    new FormData(form).forEach(function (v, k) { data[k] = v; });
    fetch("/backend/api/contact.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    }).then(function (res) { return res.json().then(function (j) { return { ok: res.ok, j: j }; }); })
      .then(function (r) {
        status.textContent = r.j.message || r.j.error || "Something went wrong.";
        status.className = "text-sm " + (r.ok && r.j.ok ? "text-emerald-400" : "text-red-400");
        if (r.ok && r.j.ok) form.reset();
      })
      .catch(function () {
        status.textContent = "Couldn't reach the contact server. Please email hello@samueladewale.com.ng.";
        status.className = "text-sm text-red-400";
      })
      .finally(function () { if (btn) btn.textContent = "Send Message"; });
  });
})();
