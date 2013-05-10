<?php
        if (!detect_ie()) { $class = 'class="outbound"'; }
        else { $class = 'target="NEW"'; }
?>
            <div id="miniNav" style="text-align:center;padding-bottom:10px;">
              <a href="/chapter-info.php">Chapter Info</a> | <a <?php echo $class; ?> title="Complete and Print the Enrollment Form" href="enroll.php">Join us!</a> | <a href="/chapter-officers.php">Our Officers</a> | <a href="/chapter-officer-responsibilities.php">Officer Duties</a> | <a <?php echo $class; ?> title="New Orleans H.O.G. Annual Charter Document" href="annual-charter.php" >Annual Charter</a>
            </div>
