<footer class="bg-dark text-white text-center py-5 mt-auto w-full">
    <div class="container">
        <p class="mb-4">&copy; <?php echo date("Y"); ?> CITE DEPARTMENT. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <a href="https://www.facebook.com/zyril.evangelista.9" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none">Zyril Evangelista</a>
            <a href="https://www.facebook.com/ernest.ramones.3" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none">King Ernest Ramones</a>
            <a href="https://www.facebook.com/paulo.mio.cortez.panopio" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none">Paulo Mio Panopio</a>
        </div>
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".text-white.text-decoration-none").forEach(function(link) {
            link.addEventListener("click", function(event) {
                event.preventDefault();
                const url = event.target.href;
                window.open(url, '_system');
            });
        });
    });
</script>
