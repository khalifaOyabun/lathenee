<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sélection des agences</title>
    
    <link rel="stylesheet" href="{$smarty.const.CSS_PATH_BE}/iconfonts/material-design-iconic/css/material-design-iconic-font.min.css">
    <!-- BASE CSS -->
    <link rel="stylesheet" href="{$smarty.const.CSS_PATH_BE}/changement_mot_de_passe.css">
    <!-- END OF BASE CSS INJECT -->
</head>

<body class="loginboardWrap -page-change-of-password">
    
    <div class="container">

        <form action="" method="POST" role="form" class="loginboard text-center" >
            <div class="loginboard-header">
                <h2 class="title font-weight-light animated fadeInUp ">CHANGEMENT DE VOTRE MOT DE PASSE</h2>
                <div class="brandlogo bg-transparent animated flipInX rounded-circle" style="background-image:url('{$smarty.const.MED_PATH_BE}/users/default.jpg'); background-position:center center;background-size:cover;background-repeat: none;"></div>
            </div>
            <div class="loginboard-body content-font--regular mt-lg-5">
                <h6><small>Veuillez insérer ci-après votre nouveau mot de passe</small></h6>
                <span class="fields-group-shadow animated">
                    <div class="password animated fadeInUp" style="animation-delay:0.5s;"> <input type="password" class="form-control" id="password" name='password' placeholder="Nouveau mot de passe" required="required"> </div>
                    <div class="password animated fadeInUp" style="animation-delay:0.6s;"> <input type="password" class="form-control" id="passwords" name='confirmation_password' placeholder="Confirmer nouveau mot de passe" required="required"> </div>
                </span>
            </div>
            <div class="loginboard-footer row animated fadeInUp" style="animation-delay:0.8s;">
                <div class="col-lg-12 logbutton mt-lg-4">
                    <button type="submit" class="btn btn-primary">Sauvegarder le nouveau mot de passe</button> 
                </div>
            </div>
        </form>

    </div>

</body>
</html>


