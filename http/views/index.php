<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CSV & MySQL app</title>
    <meta name="description" content="CSV & MySQL app">
    <meta name="author" content="Alex">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

    <script src='http://<?= $m['assets_path']; ?>/assets/homepage.js'></script>
</head>

<body>
    <div class='container'>
        <div class='card my-3'>
            <div class='card-header'>
                <h1>Actual Sales CSV upload, parsing and loading from DB App</h1>
            </div>
        </div>
        <div class='card my-3'>
            <div class='card-header'>
                <h2>Table Management</h2>
            </div>
            <div class='card-body'>
                <div class='form-row'>
                    <div class='col-auto mr-auto'><button data-btn-self-init-ajax='createTable' class=' btn btn-success'><b>+</b> Create All Tables </button></div>
                    <div class='col-auto ml-auto'><button data-btn-self-init-ajax='deleteTable' class=' btn btn-danger'><b>X</b> Delete All Tables</button></div>
                </div>
            </div>
        </div>
        <div class='card my-3'>
            <div class='card-header'>
                <h2>Upload CSV</h2>
            </div>
            <div class='card-body'>
                <div class='form-row p-2 dropzone' style="border:2px dashed black;">
                    <div class='col-12 bg-ligth'>You don't need to upload a file. If a file input is empty, the file will be downloaded from a server.</div>
                    <div class='col-12 bg-ligth '>Attach a file using input, or drag and drop.</div>
                    <div class='col-12 mr-auto default-url'><input type='text' class='w-100 form-control mb-3' disabled value=<?= $m['csv_url']; ?> /></div>
                    <div class='col-auto mr-auto'><label>Optional file<input type='file' class='form-control-file' name='csv' /></label></div>
                    <div class='col-auto align-self-center'><button name='uploadCsv' class=' btn btn-success'><b>+</b> Upload CSV from URL</button></div>
                </div>
            </div>
        </div>
        <div class='card my-3'>
            <div class='card-header'>
                <h2>Deals Log Table</h2>
            </div>
            <div class='card-body'>
                <form name='load_deals_log_form' method="POST">
                    <div class='form-row'>
                        <label class="col-12 col-md-3">From<input class='form-control' value='<?= $m['now']; ?>' type='date' name='from' /></label>
                        <label class="col-12 col-md-3">To<input value='<?= $m['now']; ?>' type='date' name='to' class='form-control ' /></label>
                        <div class='col-12'></div>
                        <label class='col-12 col-md-3'>Client Search <input class='form-control' type='text' name='client' /></label>
                        <label class='col-12 col-md-3 mr-auto'>Deal Search <input class='form-control' type='text' name='deal' /></label>   
                        <div class='col-auto ml-auto'><button class='reload_table btn btn-primary align-self-end'>Load Logs</button></div>
                        <div class='col-12'></div>
                        <div class='col-12 '>
                            <table class=' deals_log_table w-100' id='deals_log_table'>
                                <thead>
                                    <tr>
                                        <th data-data='client'>Client</th>
                                        <th data-data='deal'>Deal</th>
                                        <th data-data='time_string' data-sort="time">Time</th>
                                        <th data-data='accepted'>Accepted</th>
                                        <th data-data='refused'>Refused</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class='col-12'></div>
                        <div class='col-auto mr-auto'></div>
                        <div class='col-auto ml-auto'><button class='reload_table btn btn-primary'>Load Logs</button></div>
                        <div class='col-12'></div>
                    </div>
                </form>
            </div>
        </div>
        <!-- <div class='card'>
            <div class='card-header'>Debugging message output</div>
            <div class='card-body'><?= $m['debug']; ?></div>
        </div> -->
    </div>
</body>

</html>