<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <footer class="bg-gray-800 text-white text-center py-6 w-full mt-auto">
        <div class="container mx-auto">
            <p class="mb-4">&copy; <?php echo date("Y"); ?> CITE DEPARTMENT. All rights reserved.</p>
            <div class="flex justify-center space-x-4">
                <a href="https://www.facebook.com/zyril.evangelista.9" class="external-link hover:underline" target="_blank" rel="noopener noreferrer">Zyril Evangelista</a>
                <a href="https://www.facebook.com/ernest.ramones.3" class="external-link hover:underline" target="_blank" rel="noopener noreferrer">King Ernest Ramones</a>
                <a href="https://www.facebook.com/paulo.mio.cortez.panopio" class="external-link hover:underline" target="_blank" rel="noopener noreferrer">Paulo Mio Panopio</a>
                <!-- <a href="https://www.facebook.com/benedict.ramirez.319" class="external-link hover:underline" target="_blank" rel="noopener noreferrer">Benedict Ramirez</a> -->
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".external-link").forEach(function(link) {
                link.addEventListener("click", function(event) {
                    event.preventDefault();
                    const url = event.target.href;
                    window.open(url, '_system');
                });
            });
        });
    </script>
</body>
</html>
