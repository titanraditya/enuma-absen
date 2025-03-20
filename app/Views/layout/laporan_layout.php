<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?=$title?> - <?=$user['name']?> (<?=date('d-m-Y')?>)</title>
        <style>
             body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 20px;
                color: #333;
                background-color: #fff;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                border: 1px solid #ddd;
                padding: 20px;
                box-shadow: 0 0 5px rgba(0,0,0,0.1);
            }
            header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header-text {
                overflow: hidden;
            }
            header h1 {
                font-size: 24px;
                margin: 0;
                padding: 0;
            }
            header p {
                font-size: 14px;
                color: #777;
            }
            #tanggal {
                font-size: 14px;
                position: absolute; 
                z-index: -1; 
                top: 65px; 
                right: 50px;
            }
            #head {
                margin-top:30px;
                font-size: 14px;
            }
            #head th {
                text-align: left;
            }
            #right {
                font-size: 14px;
                position: absolute; 
                z-index: -1; 
                top: 80px; 
                right: 35px;
            }
            #right th {
                text-align: left;
            }
            #data{
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-size: 12px;
            }
            #data, #data th, #data td {
                border: 1px solid #aaa;
            }
            #data th,#data td {
                padding: 8px;
            }
            #data th{
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
            <header>
                <img src="https://absen.enumatechnology.com/enumatech2.png" alt="Logo" style="position: absolute; z-index: -1; top: 20px; left: 30px; max-width: 150px;">
                <!-- <img src="http://localhost:8080/enumatech2.png" alt="logo-enuma" class='logo'> -->
                <div class="header-text">
                    <h1><?=$title?></h1>  
                </div>
            </header>
            <table id='head'>
                <tr>
                    <th width="50px">Nama</th>
                    <td>:</td>
                    <td width="300px"><?=$user['name']?></td>
                    <th width="110px">Tanggal Cetak</th>
                    <td>:</td>
                    <td width="170px"><?=date('d-m-Y')?></td>
                </tr>
                <tr>
                    <th>Grup</th>
                    <td>:</td>
                    <td><?=$user['category_name']?></td>
                    <th>Periode</th>
                    <td>:</td>
                    <td><?=$periode?></td>
                </tr>
            </table>
            <table id="data">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <?php if($data_show == '1') {?>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                        <?php } ?>
                        <?php if($data_show == '2') { ?>
                            <th>Status</th>
                            <th>Waktu</th>
                        <?php } ?>
                        <?php if($data_show == '3') { ?>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!$attendance) {?>
                        <tr>
                            <td colspan='5' style="text-align: center">Tidak ada data</td>
                        </tr>
                    <?php } else { foreach($attendance as $attendance) {?>
                    <tr>
                        <td><?=esc(date('Y-m-d',strtotime($attendance['created_at'])))?></td>
                        <?php if($data_show == '1') {?>
                            <td><?=esc($attendance['status'])?></td>
                            <td><?php
                                if($attendance['status'] == 'Ijin') {
                                    echo "-";
                                } else {
                                    if($attendance['no_limit'] == '1') {
                                        echo date('H:i:s',strtotime($attendance['checkin']));
                                    } else {
                                        if($attendance['checkout'] == null && date('Y-m-d',strtotime($attendance['created_at'])) == date("Y-m-d")) {
                                            echo date('H:i:s',strtotime($attendance['checkin']))." - Belum Pulang";
                                        } else if($attendance['checkout'] == null) {
                                            echo date('H:i:s',strtotime($attendance['checkin']))." - Tidak Absen";
                                        } else {
                                            echo date('H:i:s',strtotime($attendance['checkin']))." - ".date('H:i:s',strtotime($attendance['checkout']));
                                        }
                                    }
                                }
                            ?></td>
                            <td><?=esc($attendance['activity'])?></td>
                            <td><?=esc($attendance['description'])?></td>
                        <?php } ?>
                        <?php if($data_show == '2') { ?>
                            <td><?=esc($attendance['status'])?></td>
                            <td><?php
                                if($attendance['status'] == 'Ijin') {
                                    echo "-";
                                } else {
                                    if($attendance['no_limit'] == '1') {
                                        echo date('H:i:s',strtotime($attendance['checkin']));
                                    } else {
                                        if($attendance['checkout'] == null && date('Y-m-d',strtotime($attendance['created_at'])) == date("Y-m-d")) {
                                            echo date('H:i:s',strtotime($attendance['checkin']))." - Belum Pulang";
                                        } else if($attendance['checkout'] == null) {
                                            echo date('H:i:s',strtotime($attendance['checkin']))." - Tidak Absen";
                                        } else {
                                            echo date('H:i:s',strtotime($attendance['checkin']))." - ".date('H:i:s',strtotime($attendance['checkout']));
                                        }
                                    }
                                }
                            ?></td>
                        <?php } ?>
                        <?php if($data_show == '3') { ?>
                            <td><?=esc($attendance['activity'])?></td>
                            <td><?=esc($attendance['description'])?></td>
                        <?php } ?>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
    </body>
</html>