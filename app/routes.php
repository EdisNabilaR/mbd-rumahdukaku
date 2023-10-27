<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    
    // call ReadKeluarga -- untuk membaca data dari tabel Keluarga
    $app->get('/Keluarga', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM Keluarga');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // call CreateKeluarga -- untuk membuat entri dalam tabel Keluarga
    $app->post('/Keluarga', function (Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
    
            if (
                empty($parseBody['ID_Keluarga']) ||
                empty($parseBody['Nama_Keluarga']) ||
                empty($parseBody['Alamat']) ||
                empty($parseBody['No_Telpon'])
            ) {
                throw new Exception("Harap isi semua field.");
            }
    
            $idKeluarga = $parseBody['ID_Keluarga'];
            $namaKeluarga = $parseBody['Nama_Keluarga'];
            $alamat = $parseBody['Alamat'];
            $noTelpon = $parseBody['No_Telpon'];
    
            $db = $this->get(PDO::class);
            $query = $db->prepare('INSERT INTO Keluarga (ID_Keluarga, Nama_Keluarga, Alamat, No_Telpon) VALUES (?, ?, ?, ?)');
    
            $query->execute([$idKeluarga, $namaKeluarga, $alamat, $noTelpon]);
    
            $response->getBody()->write(json_encode(['message' => 'Data Keluarga Tersimpan Dengan ID ' . $idKeluarga]));
    
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

    
    // call UpdateKeluarga -- untuk memperbarui data dalam tabel Keluarga
    $app->put('/Keluarga/{ID_Keluarga}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $currentId = $args['ID_Keluarga'];
        $namaKeluarga = $parsedBody["Nama_Keluarga"];
        $alamat = $parsedBody["Alamat"];
        $noTelpon = $parsedBody["No_Telpon"];

        $db = $this->get(PDO::class);

        $query = $db->prepare('UPDATE Keluarga SET Nama_Keluarga = ?, Alamat = ?, No_Telpon = ? WHERE ID_Keluarga = ?');
        $query->execute([$namaKeluarga, $alamat, $noTelpon, $currentId]);

        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Data Keluarga dengan ID ' . $currentId . ' telah diperbarui'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal memperbarui data Keluarga dengan ID ' . $currentId
                ]
            ));
        }

        return $response->withHeader("Content-Type", "application/json");
    });

    
    // Call DeleteKeluarga -- untuk menghapus data dari tabel Keluarga
    $app->delete('/Keluarga/{ID_Keluarga}', function (Request $request, Response $response, $args) {
        $currentId = $args['ID_Keluarga'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('DELETE FROM Keluarga WHERE ID_Keluarga = ?');
            $query->execute([$currentId]);
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data Keluarga dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data Keluarga dengan ID ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    
//=============================================================================================================//     
    //Call ReadJenazah -- untuk membaca data dari tabel Jenazah
    $app->get('/Jenazah', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM Jenazah');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });


    //Call CreateJenazah -- untuk membuat entri baru dalam tabel Jenazah
    $app->post('/Jenazah', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['ID_Jenazah']) ||
                empty($parseBody['ID_Keluarga']) ||
                empty($parseBody['Nama_Jenazah']) ||
                empty($parseBody['Tgl_Lahir']) ||
                empty($parseBody['Tgl_Meninggal']) ||
                empty($parseBody['Lokasi_Meninggal'])
            ) {
                throw new Exception("Harap isi semua field.");
            }

            $idJenazah = $parseBody['ID_Jenazah'];
            $idKeluarga = $parseBody['ID_Keluarga'];
            $namaJenazah = $parseBody['Nama_Jenazah'];
            $tglLahir = $parseBody['Tgl_Lahir'];
            $tglMeninggal = $parseBody['Tgl_Meninggal'];
            $lokasiMeninggal = $parseBody['Lokasi_Meninggal'];

            $db = $this->get(PDO::class);
            $query = $db->prepare('INSERT INTO Jenazah (ID_Jenazah, ID_Keluarga, Nama_Jenazah, Tgl_Lahir, Tgl_Meninggal, Lokasi_Meninggal) VALUES (?, ?, ?, ?, ?, ?)');

            $query->execute([$idJenazah, $idKeluarga, $namaJenazah, $tglLahir, $tglMeninggal, $lokasiMeninggal]);

            $response->getBody()->write(json_encode(['message' => 'Data Jenazah Tersimpan Dengan ID ' . $idJenazah]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

     
    //Call UpdateJenazah -- untuk memperbarui data dalam tabel Jenazah
    $app->put('/Jenazah/{ID_Jenazah}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        
        $currentId = $args['ID_Jenazah'];
        $namaJenazah = $parsedBody["Nama_Jenazah"];
        $tglLahir = $parsedBody["Tgl_Lahir"];
        $tglMeninggal = $parsedBody["Tgl_Meninggal"];
        $lokasiMeninggal = $parsedBody["Lokasi_Meninggal"];

        $db = $this->get(PDO::class);
        
        $query = $db->prepare('UPDATE Jenazah SET Nama_Jenazah = ?, Tgl_Lahir = ?, Tgl_Meninggal = ?, Lokasi_Meninggal = ? WHERE ID_Jenazah = ?');
        $query->execute([$namaJenazah, $tglLahir, $tglMeninggal, $lokasiMeninggal, $currentId]);
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Data Jenazah dengan ID ' . $currentId . ' telah diperbarui'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal memperbarui data Jenazah dengan ID ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    
    //Call DeleteJenaxah -- untuk menghapus data dari tabel Jenazah berdasarkan ID
    $app->delete('/Jenazah/{ID_Jenazah}', function (Request $request, Response $response, $args) {
        $currentId = $args['ID_Jenazah'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('DELETE FROM Jenazah WHERE ID_Jenazah = ?');
            $query->execute([$currentId]);
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data Jenazah dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data Jenazah dengan ID ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });


//=============================================================================================================//
    // call ReadPelayanan untuk membaca data dari tabel Pelayanan
    $app->get('/Pelayanan', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM Pelayanan');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // call CreatePelayanan untuk membuat entri baru dalam tabel Pelayanan
    $app->post('/Pelayanan', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['ID_Pelayanan']) ||
                empty($parseBody['ID_Jenazah']) ||
                empty($parseBody['Tgl_Pelayanan']) ||
                empty($parseBody['Jenis_Pelayanan'])
            ) {
                throw new Exception("Harap isi semua field.");
            }

            $ID_Pelayanan = $parseBody['ID_Pelayanan'];
            $ID_Jenazah = $parseBody['ID_Jenazah'];
            $Tgl_Pelayanan = $parseBody['Tgl_Pelayanan'];
            $Jenis_Pelayanan = $parseBody['Jenis_Pelayanan'];

            $db = $this->get(PDO::class);

            // Tambahkan data ke tabel Pelayanan
            $queryPelayanan = $db->prepare('INSERT INTO Pelayanan (ID_Pelayanan, ID_Jenazah, Tgl_Pelayanan, Jenis_Pelayanan) VALUES (?, ?, ?, ?)');
            $queryPelayanan->execute([$ID_Pelayanan, $ID_Jenazah, $Tgl_Pelayanan, $Jenis_Pelayanan]);

            $response->getBody()->write(json_encode(['message' => 'Data Pelayanan Tersimpan Dengan ID ' . $ID_Pelayanan]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });

    //Call UpdatePelayanan -- untuk memperbarui data dalam tabel Pelayanan berdasarkan ID
    $app->put('/Pelayanan/{ID_Pelayanan}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        
        $currentId = $args['ID_Pelayanan'];
        $ID_Jenazah = $parsedBody["ID_Jenazah"];
        $Tgl_Pelayanan = $parsedBody["Tgl_Pelayanan"];
        $Jenis_Pelayanan = $parsedBody["Jenis_Pelayanan"];

        $db = $this->get(PDO::class);
        
        $query = $db->prepare('UPDATE Pelayanan SET ID_Jenazah = ?, Tgl_Pelayanan = ?, Jenis_Pelayanan = ? WHERE ID_Pelayanan = ?');
        $query->execute([$ID_Jenazah, $Tgl_Pelayanan, $Jenis_Pelayanan, $currentId]);
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Data Pelayanan dengan ID ' . $currentId . ' telah diperbarui'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal memperbarui data Pelayanan dengan ID ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

    //Call DeletePelayanan -- untuk menghapus data dari tabel Pelayanan berdasarkan ID
    $app->delete('/Pelayanan/{ID_Pelayanan}', function (Request $request, Response $response, $args) {
        $currentId = $args['ID_Pelayanan'];
        $db = $this->get(PDO::class);
        
        try {
            $query = $db->prepare('DELETE FROM Pelayanan WHERE ID_Pelayanan = ?');
            $query->execute([$currentId]);
        
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data Pelayanan dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data Pelayanan dengan ID ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });

//==============================================================================================================//

    // call ReadPetugas -- untuk membaca data dari tabel Petugas
    $app->get('/Petugas', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM Petugas');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });


    // call CreatePetugas -- untuk membuat entri baru dalam tabel Petugas
    $app->post('/Petugas', function(Request $request, Response $response) {
        try {
            $parseBody = $request->getParsedBody();
            if (
                empty($parseBody['ID_Petugas']) ||
                empty($parseBody['ID_Pelayanan']) ||
                empty($parseBody['Nama_Petugas']) ||
                empty($parseBody['Jabatan'])
            ) {
                throw new Exception("Harap isi semua field.");
            }

            $ID_Petugas = $parseBody['ID_Petugas'];
            $ID_Pelayanan = $parseBody['ID_Pelayanan'];
            $Nama_Petugas = $parseBody['Nama_Petugas'];
            $Jabatan = $parseBody['Jabatan'];

            $db = $this->get(PDO::class);

            // Tambahkan data ke tabel Petugas
            $queryPetugas = $db->prepare('INSERT INTO Petugas (ID_Petugas, ID_Pelayanan, Nama_Petugas, Jabatan) VALUES (?, ?, ?, ?)');
            $queryPetugas->execute([$ID_Petugas, $ID_Pelayanan, $Nama_Petugas, $Jabatan]);

            $response->getBody()->write(json_encode(['message' => 'Data Petugas Tersimpan Dengan ID ' . $ID_Petugas]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $errorResponse = ['error' => $e->getMessage()];
            $response = $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    });


    //Call UpdatePetugas -- untuk memperbarui data dalam tabel Petugas berdasarkan ID_Petugas
    $app->put('/Petugas/{ID_Petugas}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        
        $currentId = $args['ID_Petugas'];
        $ID_Pelayanan = $parsedBody['ID_Pelayanan'];
        $Nama_Petugas = $parsedBody['Nama_Petugas'];
        $Jabatan = $parsedBody['Jabatan'];

        $db = $this->get(PDO::class);
        
        $query = $db->prepare('UPDATE Petugas SET ID_Pelayanan = ?, Nama_Petugas = ?, Jabatan = ? WHERE ID_Petugas = ?');
        $query->execute([$ID_Pelayanan, $Nama_Petugas, $Jabatan, $currentId]);
        
        if ($query) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Data Petugas dengan ID ' . $currentId . ' telah diperbarui'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Gagal memperbarui data Petugas dengan ID ' . $currentId
                ]
            ));
        }
        
        return $response->withHeader("Content-Type", "application/json");
    });


    //Call DeletePetugas -- untuk menghapus data dalam tabel Petugas berdasarkan ID_Petugas
    $app->delete('/Petugas/{ID_Petugas}', function (Request $request, Response $response, $args) {
        $currentId = $args['ID_Petugas'];
        $db = $this->get(PDO::class);

        try {
            $query = $db->prepare('DELETE FROM Petugas WHERE ID_Petugas = ?');
            $query->bindParam(1, $currentId, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Petugas dengan ID ' . $currentId . ' tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Petugas dengan ID ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }

        return $response->withHeader("Content-Type", "application/json");
    });
