<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CSV & MySQL app</title>
    <meta name="description" content="CSV & MySQL app">
    <meta name="author" content="Alex">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" >
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
    <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" ></script>

    <script src='http://<?=$m['assets_path'];?>/assets/homepage.js' ></script>
</head>

<body>
    <div class='container'>
        <div class='card'>
            <div class='card-header'>Table Management</div>
            <div class='card-body'>
                <div class='form-row'>
                    <div class='col-auto mr-auto'><button data-btn-self-init-ajax='createTable' class=' btn btn-success'><b>+</b> Create table</button></div>
                    <div class='col-auto ml-auto'><button data-btn-self-init-ajax='deleteTable' class=' btn btn-danger'><b>X</b> Delete table</button></div>
                </div>
                <div class='row'>
                    <button class=' reload_table_btn'>TEST BUTTON</button>
                </div>
            </div>
        </div>
        <div class='card'>
            <div class='card-header'>Debugging message output</div>
            <div class='card-body'><?= $m['debug']; ?></div>
        </div>
    </div>
</body>

</html>