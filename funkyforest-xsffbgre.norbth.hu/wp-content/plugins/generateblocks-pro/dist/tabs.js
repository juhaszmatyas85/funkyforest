(()=>{const t=t=>{const e=t.closest(".gb-tabs");if(!e)return;const s=[...t.parentElement.children].filter((t=>t.classList.contains("gb-tabs__button"))).indexOf(t),n=e.querySelector(".gb-tabs__items").querySelectorAll(":scope > .gb-tabs__item"),a=n[s];if(!a||a.classList.contains("gb-tabs__item-open"))return;const i=e.querySelector(".gb-tabs__buttons").querySelectorAll(".gb-tabs__button");n.forEach((t=>{t.classList.remove("gb-tabs__item-open")})),i.forEach((t=>{t.setAttribute("aria-expanded",!1),t.classList.remove("gb-block-is-current")}));const r=i[s];a.classList.add("gb-tabs__item-open"),a.classList.add("gb-tabs__item-transition"),setTimeout((()=>a.classList.remove("gb-tabs__item-transition")),100),e.setAttribute("data-opened-tab",s+1),r.setAttribute("aria-expanded",!0),r.classList.add("gb-block-is-current")};function e(){const t=window.location.hash;if(t){const e=document.getElementById(String(t).replace("#",""));if(e&&e.classList.contains("gb-tabs__item")){const t=e.closest(".gb-tabs").querySelector(".gb-tabs__buttons"),s=[...e.parentElement.children].indexOf(e);t&&t.children&&t.children[s]&&(t.children[s].click(),t.scrollIntoView())}}}document.querySelectorAll(".gb-tabs__button").forEach((e=>{e.addEventListener("click",(()=>t(e))),"BUTTON"!==(null==e?void 0:e.tagName.toUpperCase())&&e.addEventListener("keydown",(s=>{" "!==s.key&&"Enter"!==s.key&&"Spacebar"!==s.key||(s.preventDefault(),t(e))}))})),document.addEventListener("DOMContentLoaded",e),window.addEventListener("hashchange",e);const s=document.querySelectorAll(".gb-tabs__item");s&&s.forEach((t=>{const e=t.closest(".gb-tabs").querySelector(".gb-tabs__buttons").querySelectorAll(".gb-tabs__button")[[...t.parentElement.children].indexOf(t)];if(!e)return;const s=e.getAttribute("id");s&&t.setAttribute("aria-labelledby",s);const n=t.getAttribute("id");n&&e.setAttribute("aria-controls",n),t.classList.contains("gb-tabs__item-open")&&e?e.setAttribute("aria-expanded",!0):e&&e.setAttribute("aria-expanded",!1)}))})();