

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
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            background: #f1f5f9;
            color: #1e293b;
        }

        

        /* ENCABEZADO */

    

        
        .header {
            background: #172554;
            color: white;
            padding: 15px 5%;
            min-height: 115px;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo {
            width: 85px;
            height: 85px;
            object-fit: contain;
            background: white;
            border-radius: 10px;
            padding: 5px;
        }

        .header h2 {
            margin: 0 0 8px;
            font-size: 25px;
            letter-spacing: 1px;
        }

        .header p {
            margin: 0;
            font-size: 16px;
            color: #dbeafe;
        }
    <header class="header">
    <div class="header-content">

        <img
            src="{{ asset('images/image.png') }}"
            alt="Logo Jenalex Dinamic"
            class="logo"
        >

        <div>
            <h2>JENALEX DINAMIC</h2>
            <p>Inventario</p>
        </div>

    </div>
</header>
        

        /* CONTENIDO PRINCIPAL */
        main {
            max-width: 1450px;
            margin: 35px auto;
            padding: 0 25px;
        }

        .card {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06);
        }

        h1 {
            color: #172554;
            font-size: 30px;
            margin-top: 0;
            margin-bottom: 18px;
        }

        h3 {
            color: #172554;
        }

        /* BOTONES */
        .btn {
            display: inline-block;
            padding: 11px 18px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 7px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #64748b;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        /* FORMULARIOS */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: #172554;
            font-size: 15px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #ffffff;
            font-size: 15px;
            color: #1e293b;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .form-group textarea {
            resize: vertical;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
        }

        /* TABLAS */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }

        th, td {
            padding: 13px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background: #f1f5f9;
            color: #172554;
            font-weight: 700;
        }

        /* MENSAJES */
        .alert {
            padding: 13px 16px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* RESPONSIVE */
        @media (max-width: 650px) {
            .header {
                padding: 15px 20px;
            }

            .header-content {
                gap: 12px;
            }

            .logo {
                width: 65px;
                height: 65px;
            }

            .header h2 {
                font-size: 19px;
            }

            main {
                margin: 20px auto;
                padding: 0 12px;
            }

            .card {
                padding: 18px;
            }

            h1 {
                font-size: 23px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: auto;
            }

            table {
                font-size: 13px;
            }
        }
        .header-content {
    display: flex;
    align-items: center;
    gap: 18px;
}

.logo {
    width: 85px;
    height: 85px;
    object-fit: contain;
    background: white;
    border-radius: 10px;
    padding: 5px;
}
    </style>
</head>

<body>

    <header class="header">
        <div class="header-content">

            <img
                src="{{ asset('images/image.png') }}"
                alt="Logo Jenalex Dinamic"
                class="logo"
            >

            <div>
                <h2>JENALEX DINAMIC</h2>
                <p>Inventario</p>
            </div>

        </div>
    </header>

    <main>
        @yield('content')
    </main>

</body>
</html>
