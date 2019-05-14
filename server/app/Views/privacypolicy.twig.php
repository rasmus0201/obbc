<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Privatlivspolitik</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div id="app">
        <div class="container-fluid my-4">
            {% if flashes is not empty %}
                {% for flash in flashes %}
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            {% if flash.status == 'error' %}
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ flash.msg }}
                            {% elseif flash.status == 'success' %}
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ flash.msg }}
                            {% elseif flash.status == 'warning' %}
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ flash.msg }}
                            {% else %}
                                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                                    {{ flash.msg }}
                            {% endif %}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Luk">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                {% endfor %}
            {% endif %}

            <h1>Privatlivspolitik</h1>
            <p>
                Fortegnelse over behandling af personoplysninger.
                <br>
                Dato for seneste ajourføring af dokumentet: 14.08.2018
            </p>

            <p class="text-warning">
                Den korte version:
            </p>
            <p class="mb-5">
                Vi gemmer kun din log-ind detaljer (medlemsnr/tlf eller email og adgangskode) på din egen telefon/enhed. Hvis der skulle ske et datalæk vil vi foresøge at underette de involverede parter - chancen for dette er dog minimal når vi ikke gemmer data, vi behandler dem kun så du får en fremragende App til at melde dig på hold :)
            </p>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td>
                            Hvem har ansvaret for databeskyttelse for brug af App'en?
                        </td>
                        <td>
                            Rasmus Sørensen, tlf: 31755650, mail: rasmus@it-lease.dk
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Hvad er formålene med behandlingen?
                        </td>
                        <td>
                            <ol>
                                <li>
                                    Behandling af login-oplysninger til træningscenteret
                                </li>
                                <li>
                                    Behandling af data fra træningscenteret. Holdinformationer inkluderende navne, tidspunkter, steder og antal tilmeldte, mv.
                                </li>
                            </ol>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Hvilke personoplysninger behandler vi?
                        </td>
                        <td>
                            Almindelige personoplysninger:
                            <ol>
                                <li>
                                    Navn
                                </li>
                                <li>
                                    Email
                                </li>
                                <li>
                                    Telefon
                                </li>
                                <li>
                                    Medlemsnr.
                                </li>
                                <li>
                                    Adgangskode
                                </li>
                                <li>
                                    Holdtilmeldinger
                                </li>
                            </ol>
                            Oplysninger, der er tillagt en højere grad af beskyttelse:
                            <ol>
                                <li class="font-italic">Ingen</li>
                            </ol>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Hvem behandler vi oplysninger om?
                        </td>
                        <td>
                            <ol>
                                <li>Medlemmer</li>
                                <li>Ansatte</li>
                                <li>Trænere</li>
                                <li>Frivillige</li>
                            </ol>
                            (Alle personer som kan indgå i kalenderen)
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Hvem videregives oplysningerne til?
                        </td>
                        <td>
                            Data videresendes og behandles af Flexybox.com (FlexyFitness)
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Hvornår sletter vi personoplysninger?
                        </td>
                        <td>
                            Personoplysninger bliver kun gemt på brugerens egen enhed. Der vil mens brugeren har App'en åbent blive sendt personoplysninger til vores servere og videre til Flexybox.com, men vil aldrig gemmes i hukommelse.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Hvordan opbevarer vi personoplysninger i foreningen?
                        </td>
                        <td>
                            <ul>
                                <li>Brugerens enhed (smartphone/browser)</li>
                                <li>Alle personoplysninger når man bruger App'en vil blive behandlet af en server i Frankfurt, Tyskland</li>
                                <li>Det er ikke muligt for andre end brugeren selv at se personoplysninger behandlet af App'en. Flexybox.com opbevarer alle personoplysninger</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>Hvad gør vi, hvis der sker et brud på persondatasikkerheden?</td>
                        <td>
                            <p>Hvis alle eller nogle af de registrerede oplysninger bliver stjålet, hacket eller på anden måde kompromitteret, kontakter vi vores hovedorganisation og drøfter eventuel anmeldelse til politiet og til Datatilsynet.</p>
                            <p>Der vil komme en notits inde i App'en (efter log-in). Det vil også altid være muligt at gå til denne side og se om der er nogle nyheder/informationer.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Hvad kan vores IT-system, og har vi tænkt databeskyttelse ind i vores IT-systemer?</td>
                        <td>
                            Vores IT-system kan følgende:
                            <ul>
                                <li>
                                    Hente data fra Flexybox.com
                                </li>
                                <li>
                                    Behandle data fra Flexybox.com
                                </li>
                                <li>
                                    Gemme behandlet data midlertidigt i RAM (server), mens web-forespørgsel er igang.
                                </li>
                                <li>
                                    Vise data fra Flexybox.com
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script type="text/javascript" src="main.js"></script>
</body>
</html>
