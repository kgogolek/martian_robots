<?php
    session_start();
    include_once('Controllers/Mission.php');
    $mission = new Mission;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['drive'])) {
            $mission->start($_POST);
        }
        if (isset($_POST['clear'])) {
            $mission->clear();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="css/style.css" rel="stylesheet" media="screen">
        <title>Martian Robots</title>
    </head>
    <body>
        <div class="container">
            <h1>Insert Grid coordinates:</h1>
            <form method="POST" action="" class="form-horizontal">
                <? if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drive']) && $mission->hasError('grid-y') === false && $mission->hasError('grid-x') === false) { ?>
                    <div class="control-group">
                        <label class="control-label" for="grid-x">Grid right coordinate:</label>
                        <div class="controls"><input type="hidden" name="grid-x" value="<?=$_POST['grid-x']?>" /><?=$_POST['grid-x']?></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="grid-y">Grid upper coordinate:</label>
                        <div class="controls"><input type="hidden" name="grid-y" value="<?=$_POST['grid-y']?>"/><?=$_POST['grid-y']?></div>
                    </div>
                <? } else { ?> 
                    <div class="control-group <? if($mission->hasError('grid-x')) echo "error"; ?>">
                        <label class="control-label" for="grid-x">Grid right coordinate:</label>
                        <div class="controls"><input type="text" name="grid-x" /><? echo $mission->getError('grid-x') ?></div>
                    </div>
                    <div class="control-group <? if($mission->hasError('grid-y')) echo "error"; ?>">
                        <label class="control-label" for="grid-y">Grid upper coordinate:</label>
                        <div class="controls"><input type="text" name="grid-y" /><? echo $mission->getError('grid-y') ?></div>
                    </div>
                <? } ?>
                <hr/>
            
                <div class="control-group <? if($mission->hasError('position')) echo "error"; ?>">
                    <label class="control-label" for="start-position">Initial Position:</label>
                    <div class="controls"><input type="text" name="position" placeholder="1 1 E" value="<?=$_POST['position'] ?>"/>
                        <? echo $mission->getError('position') ?></div>
                    <p class="muted">A position consists of two integers specifying the initial coordinates of the robot and
                    an orientation (N, S, E, W), all separated by whitespace on one line. First digit is the X (right) coordinate, and the second Y coordinate (upper)</p>
                </div>
                <div class="control-group <? if($mission->hasError('instructions')) echo "error"; ?>">
                    <label class="control-label" for="start-position">Robot Instructions:</label>
                    <div class="controls"><input type="text" name="instructions" placeholder="RFFLFF" value="<?=$_POST['instructions'] ?>"/>
                    <? echo $mission->getError('instructions') ?></div>
                    <p class="muted">A robot instruction is a
                    string of the letters “L”,“R”, and “F” on one line</p>
                </div>
                <input type="submit" name="drive" class="btn btn-success" value="Go Robot, Go!"/>
                <input type="submit" name="clear" class="btn btn-danger" value="Clear" />
            </form>
            
            <hr/>
            <? if ($mission->hasErrors() === false && isset($_POST['drive'])) { ?>
                <div class="alert alert-success">Robot traveled to: <strong><?=$mission->getRobot()?></strong></div>
            <? } ?>
        </div>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
