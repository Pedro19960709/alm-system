<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>UNEDL | ALMACEN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #39346f;
        }
        h3 {
            color: #39346f;
        }
        p {
            line-height: 1.6;
        }
        hr {
            color: #c79636;
        }
        .header-table{
            margin-left: 3.5rem;
        }
        .button {
            display: inline-block;
            background-color: #c79636;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td>
                    <h1>Formato de Entrega <br> <span style="color: #c79636">UNEDL ALMACEN</span></h1>
                </td>
                <td>
                    <img src="{{ public_path('img/Escudo.png') }}"  alt="UNEDL">
                </td>
            </tr>
        </table>

        <p>
        Estimado(a) {{ $firstHistory->User->name }},
        </p>
        <p>
            A continuación te presentamos la información general de tu encargo favor de revisar el presente y verificar que los artículos que estás recibiendo vienen en perfecto estado.
        </p>

        <hr>
        <h2 style="color: #39346f">Información</h2>
        <p><span style="font-weight: bold;">Departamento:</span> {{ $petition->Department->name }}</p>
        <p><span style="font-weight: bold;">Área:</span> {{ $petition->Area->name }}</p>
        <p><span style="font-weight: bold;">Artículo:</span> {{ $petition->Article->code.' - '.$petition->Article->name.' ('.$petition->Article->quantity.' '.$petition->Article->MeasurementUnit->symbol.')' }}</p>
        <p><span style="font-weight: bold;">Total de productos:</span> {{ $petition->ordered_articles }}</p>
        <p><span style="font-weight: bold;">Artículos entregados:</span> {{ $lastHistory->delivered_articles }}</p>
        <p><span style="font-weight: bold;">Artículos restantes:</span> {{ $petition->remaining_articles }}</p>
        <p><span style="font-weight: bold;">Fecha de petición:</span> {{ $petition->created_at }}</p>
        <p><span style="font-weight: bold;">Fecha de entrega:</span> {{ $lastHistory->created_at }}</p>
        <p><span style="font-weight: bold;">ID seguimiento:</span> {{ $lastHistory->petition_id }}</p>

        <hr style="color: #c79636">
        <br>
        <br>
        <br>
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td style="padding: 2rem">
                        <hr style="width: 80%; color:#c79636">
                        <p style="text-align: center; padding-top: 12px; color: #39346f">
                            Atentamente<br/>
                            <strong style="color: #39346f">ENCARGADO DE ALMACEN</strong>
                        </p>
                    </td>
                    <td style="padding: 2rem">
                        <hr style="width: 80%; color:#c79636">
                        <p style="text-align: center; padding-top: 12px; color: #39346f">
                            Recibió<br/>
                            <strong style="color: #39346f">{{ strtoupper($petition->Department->name).' '.strtoupper($petition->Area->name) }}</strong>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>