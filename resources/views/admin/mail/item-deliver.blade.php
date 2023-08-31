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
            color: #e4c459;
        }
        .header {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;

        }
        .link {
            text-align: center;
        }
        .button {
            background-color: #39346f;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{asset('img/Escudo.png')}}">
            <h1 style="margin-top: 5.5rem;"><a class="button" href="{{ URL::to('/login') }}">UNEDL | ALMACEN</a></h1>
        </div>
        <hr>
        <p>
        Estimado(a) {{ $firstHistory->User->name }},
        </p>
        <p>
            Esperamos que te encuentres bien. Nos alegra informarte que el proceso de entrega del artículo que realizaste se ha completado exitosamente. 
            Asegurate de imprimir el formato que viene adjutado para firmar de entregado.
        </p>

        <div class="footer">
            <p>Gracias. No responder a este correo</p>
            <p>© 2023 Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>