document.addEventListener("DOMContentLoaded", () => {
    // Get current page name
    const pageName = window.location.pathname.split("/").pop();

    // Collect all unique classes
    const elements = document.querySelectorAll("[class]");
    const usedClasses = new Set();

    elements.forEach(el => {
        el.className.split(/\s+/).forEach(cls => {
            if (cls.trim() !== "") usedClasses.add("." + cls.trim());
        });
    });

    const classArray = [...usedClasses];

    // Log in console
    console.log("===== CSS Tracker =====");
    console.log("Page:", pageName);
    console.log("Classes:", classArray);
    console.log("=======================");

    // Send to server
    fetch("/driving_school/includes/log_css_tracker.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            page: pageName,
            classes: classArray
        })
    })
    .then(res => res.json())
    .then(data => console.log("Tracker saved:", data))
    .catch(err => console.error("Tracker error:", err));
});

