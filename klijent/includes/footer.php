            </div>
        </main>
    </div>
    <script>
        const btn = document.getElementById('hamburgerBtn');
        const sidebar = document.querySelector('.klijent-sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if(btn) {
            btn.onclick = () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('active');
            };
        }

        if(overlay) {
            overlay.onclick = () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('active');
            };
        }
    </script>
</body>
</html>