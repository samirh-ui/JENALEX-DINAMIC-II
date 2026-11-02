
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') | Jenalex Dinamic</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f3f5f9;
            color: #263244;
        }

        header {
            background-color: #172b4d;
            color: white;
            padding: 20px 5%;
        }

        header h2 {
            margin: 0;
        }

        nav {
            margin-top: 12px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }

        main {
            max-width: 1200px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #0000000d;
        }

        h1 {
            margin-top: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-secondary {
            background: #64748b;
        }

        .btn-danger {
            background: #dc2626;
        }

        .form-group {
            margin-bottom: 17px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 5px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f8fafc;
        }

        .actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }

        .pagination {
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            header {
                padding: 18px;
            }

            main {
                margin-top: 20px;
                padding: 0 12px;
            }

            .card {
                padding: 16px;
            }
        }
    </style>
</head>

<body>

    <header>
        <h2>JENALEX DINAMIC</h2>

        <nav>
            <a href="{{ route('equipos.index') }}">Inventario</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

</body>
</html>
