<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Metas -->
    @if (env('IS_DEMO'))
        <x-demo-metas></x-demo-metas>
    @endif
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/use/sitelogo.png">
    <link rel="icon" type="image/png" href="../assets/img/use/sitelogo.png">
    <title>
        E-vote Central  
    </title>
    <!-- Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1" rel="stylesheet" />
    @livewireStyles

</head>

<body class="g-sidenav-show bg-gray-100">

    {{ $slot }}

    <!--   Core JS Files   -->
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                if (e.target.closest('#togglePassword')) {
                    const passwordField = document.querySelector('#password');
                    const icon = e.target.closest('#togglePassword').querySelector('i');
                    
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
        });
    </script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Auto-dismiss alerts after 3 seconds
            Livewire.on('alert-shown', () => {
                setTimeout(() => {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        if (alert) {
                            alert.classList.remove('show');
                            setTimeout(() => alert.remove(), 150);
                        }
                    });
                }, 3000);
            });
        });
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="assets/js/soft-ui-dashboard.js"></script>
    @livewireScripts
</body>

</html>
