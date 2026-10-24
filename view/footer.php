    </div>

    <!-- Sticky Student Details Component (Bottom-Left Corner) -->
    <style>
        @media print {
            #student-sticky-badge { display: none !important; }
        }
    </style>
    <div id="student-sticky-badge" style="position: fixed; bottom: 16px; left: 16px; z-index: 99999; max-width: 320px; background-color: #ffffff; border: 2px solid #000000; padding: 10px 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px;">
            <span style="background-color: #000000; color: #ffffff; font-weight: 800; font-size: 11px; padding: 2px 6px; letter-spacing: 0.5px; display: inline-block;">
                SOF/23/B2/02
            </span>
            <button type="button" onclick="var c = document.getElementById('student-badge-body'); var b = this; if (c.style.display === 'none') { c.style.display = 'block'; b.textContent = '—'; } else { c.style.display = 'none'; b.textContent = '+'; }" style="background: #ffffff; color: #000000; border: 1px solid #000000; font-size: 11px; font-weight: bold; padding: 1px 6px; cursor: pointer; line-height: 14px;" title="Collapse / Expand Details">
                —
            </button>
        </div>
        <div id="student-badge-body">
            <div style="font-weight: 800; font-size: 13px; color: #000000; margin-bottom: 2px;">
                B. Ramesh Madara
            </div>
            <div style="font-size: 11px; font-weight: 600; color: #444444; margin-bottom: 6px;">
                Take Home Individual Assignment
            </div>
            <div style="padding-top: 5px; border-top: 1px solid #e0e0e0;">
                <a href="https://classroom.google.com/u/0/c/ODc1NDc1ODA4NzE4" target="_blank" rel="noopener noreferrer" style="color: #0044cc; text-decoration: underline; font-size: 10.5px; font-weight: 600; line-height: 1.35; display: block;">
                    Enterprise System Technologies &amp; Architectures - IT 403061 - 2023/24 Batch 22023/24 - B2
                </a>
            </div>
        </div>
    </div>
</body>
</html>
