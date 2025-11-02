document.addEventListener("DOMContentLoaded", () => {
    const triggers = document.querySelectorAll(".collapsible-trigger");
    const sidebarLinks = document.querySelectorAll(".sidebar-link");

    // --- Make only top-level (non-collapsible) LIs clickable ---
    document.querySelectorAll("aside > nav > ul > li").forEach(li => {
        const link = li.querySelector(".sidebar-link");

        // Skip items that have collapsible sections (they have a .collapsible-trigger)
        const hasCollapsible = li.querySelector(".collapsible-trigger");

        if (link && !hasCollapsible) {
            li.addEventListener("click", e => {
                // Avoid triggering twice when <a> itself is clicked
                if (e.target.tagName.toLowerCase() !== "a") {
                    window.location.href = link.href;
                }
            });
        }
    });

    // --- Collapsible logic ---
    triggers.forEach(trigger => {
        trigger.addEventListener("click", function (e) {
            e.stopPropagation(); // Prevent parent <li> clicks
            const content = this.nextElementSibling;
            const icon = this.querySelector(".dropdown-icon");
            const isOpen = content.style.maxHeight && content.style.maxHeight !== "0px";

            // Close other sections
            triggers.forEach(otherTrigger => {
                if (otherTrigger !== this) {
                    const otherContent = otherTrigger.nextElementSibling;
                    const otherIcon = otherTrigger.querySelector(".dropdown-icon");

                    otherContent.style.maxHeight = "0px";
                    otherContent.classList.remove("opacity-100");
                    otherContent.classList.add("opacity-0");
                    otherIcon.classList.remove("rotate-180");
                }
            });

            // Toggle this one
            if (isOpen) {
                content.style.maxHeight = "0px";
                content.classList.remove("opacity-100");
                content.classList.add("opacity-0");
                icon.classList.remove("rotate-180");
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                content.classList.remove("opacity-0");
                content.classList.add("opacity-100");
                icon.classList.add("rotate-180");
            }
        });
    });

    // --- Highlight current link ---
    sidebarLinks.forEach(link => {
        const linkHref = link.getAttribute("href");
        if (!linkHref) return;

        const current = window.location.pathname.replace(/\/$/, "");
        const linkPath = new URL(linkHref, window.location.origin).pathname.replace(/\/$/, "");

        if (current === linkPath || current.startsWith(linkPath)) {
            // Highlight active link
            link.classList.add("bg-accent", "text-white", "font-semibold");

            const li = link.closest("li");
            if (li) {
                li.classList.add("bg-accent", "text-white");
                li.classList.remove("hover:bg-secondary-light", "hover:text-white");
            }

            // Open parent collapsible if inside one
            const collapsibleContent = link.closest(".collapsible-content");
            if (collapsibleContent) {
                const parentTrigger = collapsibleContent.previousElementSibling;
                const icon = parentTrigger.querySelector(".dropdown-icon");

                collapsibleContent.style.maxHeight = collapsibleContent.scrollHeight + "px";
                collapsibleContent.classList.remove("opacity-0");
                collapsibleContent.classList.add("opacity-100");
                icon.classList.add("rotate-180");
            }
        }
    });
});
