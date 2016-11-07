<html>
    <head><title>Contoh Koneksi Mesin Absensi Mengunakan SOAP Web Service</title></head>
    <!--<meta http-equiv="refresh" content="600; URL=./tes_getabsence.php">-->
    <body>
        <table cellspacing="2" cellpadding="2" border="1">
            <tr align="center">
                <td><B>UserID</B></td>
                <td width="200"><B>Tanggal & Jam</B></td>
                <td><B>Verifikasi</B></td>
                <td><B>Status</B></td>
            </tr>
            <?php

            function Parse_Data($data, $p1, $p2) {
                $data = " " . $data;
                $hasil = "";
                $awal = strpos($data, $p1);
                if ($awal != "") {
                    $akhir = strpos(strstr($data, $p1), $p2);
                    if ($akhir != "") {
                        $hasil = substr($data, $awal + strlen($p1), $akhir - strlen($p1));
                    }
                }
                return $hasil;
            }

            if ($IP == "")
                $IP = "182.23.82.242";
            if ($Key == "")
                $Key = "0";

            $Connect = fsockopen($IP, "5555", $errno, $errstr, 1);
            if ($Connect) {
                echo "Koneksi sukses";
                $soap_request = "<GetAttLog>
                    <ArgComKey xsi:type=\"xsd:integer\">" . $Key . "</ArgComKey>
                    <Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg>
                    </GetAttLog>";

                $newLine = "\r\n";
                fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
                fputs($Connect, "Content-Type: text/xml" . $newLine);
                fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                fputs($Connect, $soap_request . $newLine);
                $buffer = "";
                while ($Response = fgets($Connect, 1024)) {
                    $buffer = $buffer . $Response;
                }
            }
            else 
            {
                die("Koneksi Gagal"); 
            }

            $buffer = Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
            $buffer = explode("\r\n", $buffer);
            for ($a = 0; $a < count($buffer); $a++) {
                $data = Parse_Data($buffer[$a], "<Row>", "</Row>");
                $PIN = Parse_Data($data, "<PIN>", "</PIN>");
                $DateTime = Parse_Data($data, "<DateTime>", "</DateTime>");
                $Verified = Parse_Data($data, "<Verified>", "</Verified>");
                $Status = Parse_Data($data, "<Status>", "</Status>");

                if (trim($PIN==NULL)) continue;
                if ($Verified == 1)
                    $Verified = "sidik jari";
                echo "<tr>";
                echo "<td> $PIN </td>";
                echo "<td> $DateTime </td>";
                switch ($Status) {
                    case 0:
                        $Status="Check in";
                        break;
                    case 1:
                        $Status="Check out";
                        break;
                    case 2:
                        $Status="Break out";
                        break;
                    case 3:
                        $Status="Break in";
                        break;
                    case 4:
                        $Status="Out in";
                        break;
                    case 5:
                        $Status="Out out";
                        break;
                    default:
                        break;
                }
                echo "<td> $Verified </td>";
                echo "<td> $Status </td>";

                echo "</tr>";
            }


            echo "</table>";
            ?>
    </body>
</html>