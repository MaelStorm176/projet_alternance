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
                            echo "<td id='moyenne_".$matiere['id']."'><input id='note_input_".$matiere['id']."' style='width: 100%' type='text' value='' disabled></td>";
                        }
                        echo "<td><i class='fas fa-plus'></i></td>";
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
</html>

<script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous">
</script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
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
</script>

<?php
$oDB->Close();
?>