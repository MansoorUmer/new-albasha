<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('css')
    <script>
        window.APP = <?php echo json_encode([
                            'currency_symbol' => config('settings.currency_symbol'),
                            'warning_quantity' => config('settings.warning_quantity')
                        ]) ?>
    </script>
    <style>
        .status-pending {
            background-color: #fff8e1;
            color: #f39c12;
            font-weight: 600;
            border-radius: 8px;
        }

        .status-preparing {
            background-color: #e3f2fd;
            color: #3498db;
            font-weight: 600;
            border-radius: 8px;
        }

        .status-complete {
            background-color: #e8f5e9;
            color: #27ae60;
            font-weight: 600;
            border-radius: 8px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini" style="font-family: 'Inter', sans-serif;">
    <!-- Site wrapper -->
    <div class="wrapper">

        @include('layouts.partials.navbar')
        @include('layouts.partials.sidebar')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 style="font-weight: 700; color: #2d3748;">@yield('content-header')</h1>
                        </div>
                        <div class="col-sm-6 text-right">
                            @yield('content-actions')
                        </div><!-- /.col -->
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                @include('layouts.partials.alert.success')
                @include('layouts.partials.alert.error')
                @yield('content')
            </section>

        </div>
        <!-- /.content-wrapper -->

        @include('layouts.partials.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    @yield('js')
    @yield('model')
</body>

</html>
