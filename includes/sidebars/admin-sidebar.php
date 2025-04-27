<nav>
    <ul class="metismenu" id="menu">
        <li class="<?php if ($page == 'dashboard') {
            echo 'active';
        } ?>"><a href="dashboard.php"><i
                    class="ti-dashboard"></i> <span>Dashboard</span></a></li>

        <li class="<?php if ($page == 'student') {
            echo 'active';
        } ?>"><a href="student.php"><i class="ti-id-badge"></i>
                <span>Students</span></a></li>

        <li class="<?php if ($page == 'teacher') {
            echo 'active';
        } ?>"><a href="teacher.php"><i
                    class="fa fa-th-large"></i> <span>Teachers</span></a></li>

        <li class="<?php if ($page == 'hod') {
            echo 'active';
        } ?>"><a href="hod.php"><i
                    class="fa fa-sign-out"></i> <span>Hods</span></a></li>

    </ul>
</nav>