<?php
include_once ("class/_db.class.php");
$oDB = new Mysql();
$matieres = array();
$etudiants = array();
$sql = "SELECT * FROM matiere";
$matieres = $oDB->TabResSQL($sql);
$sql = "SELECT * FROM etudiant";
$etudiants = $oDB->TabResSQL($sql);
$count_matiere = count($matieres);
?>


<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="row h-100 justify-content-center align-items-center">
        <div class="col-xs-12 ">
            <div class="table-responsive " data-pattern="priority-columns">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <td></td>
                        <?php

                        foreach ($matieres as $matiere){
                            echo "<th style='width: ".(100/($count_matiere+2))."%'>".$matiere["nom"]."<br />Coeff. ".$matiere["coeff"]."</th>";
                        }

                        ?>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach ($etudiants as $etudiant){
                        echo "<tr id='ligne_'".$etudiant['id'].">";
                        echo "<td>".$etudiant["nom"]."<br />".$etudiant["prenom"]."</td>";
                        foreach ($matieres as $matiere){
                            echo "<td><input style='width: 100%' type='text' value='' disabled></td>";
                        }
                        echo "<td><button type='button' class='btn btn-add' data-toggle='modal' data-target='#exampleModalCenter' value='".$etudiant['id']."'><i class='fas fa-plus'></i></button></td>";
                        echo "</tr>";

                    }

                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="<?php echo $count_matiere+2; ?>" class="text-center">Data retrieved from <a href="http://www.infoplease.com/ipa/A0855611.html" target="_blank">infoplease</a> and <a href="http://www.worldometers.info/world-population/population-by-country/" target="_blank">worldometers</a>.</td>
                    </tr>
                    </tfoot>
                </table>
            </div><!--end of .table-responsive-->
        </div>
    </div>
</div>

</body>


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Ajout d'une note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="">
                    <input type="hidden" id="id_etudiant" name="id_etudiant" value="">
                    <div class="container">
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <label for="select_matiere">Choisissez une matière :</label>
                                <select class="form-control" id="select_matiere" name="select_matiere">
                                    <option value="-1" selected>Selectionnez une matière</option>
                                    <?php
                                    foreach ($matieres as $matiere){
                                        echo "<option value='".$matiere['id']."'>".$matiere['nom']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label>Définissez une note : </label>
                                <div class="row ">
                                    <div class="col-sm-4 ">
                                        <input type="text" id="note_input" name="note_input" class="form-control text-center" value="0"/>
                                    </div>
                                    <div class="col-sm-1">
                                        /
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" id="note_input_max" name="note_input_max" class="form-control text-center" value="0"/>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="row">
                            <div class="col-sm-12"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <button onclick="send_data()" type="button" class="btn btn-primary">Valider</button>
            </div>
        </div>
    </div>
</div>

</html>

<script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous">
</script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script type="application/javascript">
    $(document).ready( function () {
        $('.table').DataTable({
            columnDefs: [
                {   "searchable": true, "targets": 0 },
                {   "orderable": false, "targets": -1 }
            ],
            language: { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json" }
        });
    });

    $(".btn-add").click(function (){
        $("#id_etudiant").attr("value",$(this).attr("value"));
    });

    function send_data(){
        let data = $("form").serializeArray();
        /*
        alert(form);
        let data = [];
        $.each(form, function(i, field){
            data.push(field.name + ":" + field.value + " ");
        });*/

        alert(data);

        $.ajax({
            url: "moyenne.php",
            type: "post",
            data: data,
            success: function (response) {
                alert(response);
                // You will get response from your PHP page (what you echo or print)
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    function maj_moyenne(data_retour){
        alert(data_retour);
    }
</script>

<?php
$oDB->Close();
?>