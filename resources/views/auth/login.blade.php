<!doctype html>
<html lang="es">

<head>
  <title>LOGIN</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="sweetalert2.min.css">
  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
   
    <link rel="stylesheet" href="{{asset('assets/estilos.css')}}">
</head>

<body>
<section class="h-100 gradient-form" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                  <img src="{{asset('img/Escudo.png')}}"
                    style="width: 185px;" alt="logo">
                  <h4 class="mt-1 mb-5 pb-1">LOGIN</h4>
                </div>

                <form action="{{ route('postLogin') }}" method="post" id="form-control">
                    @csrf
                  <p>INICIAR SESIÓN</p>

                  <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example11">CORREO</label>
                    <input type="email" name="email" id="form2Example11" class="form-control"
                      placeholder="Ingresa tu correo"  require/>
                  </div>

                  <div class="form-outline mb-4">
                  <label class="form-label" for="form2Example22">CONTRASEÑA</label>
                    <input type="password" name="password" id="form2Example22" class="form-control" require/>
                  </div>

                  <div class="text-center pt-1 mb-5 pb-1">
                    <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit">Ingresar</button>
                  
                  </div>
                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h3 class="mb-4">Universidad Enrique Díaz de León</h3>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
</body>
<script>
  const form = document.querySelector('#form-control');
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        if(formData.get('email') == '' && formData.get('password') == '') {
            Swal.fire({
                title: 'Error!',
                text: 'Llena los campos para poder continuar!',
                icon: 'error',
                confirmButtonText: 'cerrar'
            })
            return;
        }

        fetch('login', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            sessionStorage.setItem('respuestaCache', JSON.stringify(data));
            console.log(data);
            if(data.type == 'success') {

                Swal.fire({
                    title: 'Bienvenido '+data.message+'!',
                    type: 'success',
                    timer: 1000,
                    showCancelButton: false,
                    showConfirmButton: false
                })
                setTimeout(function(){
                    document.location="{{ route('home') }}";
                }, 1250);

            }else if(data.type == 'error') {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'cerrar'
                })

            }else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'cerrar'
                })
            }
        })
        .catch(error => {
          Swal.fire({
                title: 'Error!',
                text: 'Ups algo paso!',
                icon: 'error',
                confirmButtonText: 'cerrar'
            })
        });
    });
</script>
</html>