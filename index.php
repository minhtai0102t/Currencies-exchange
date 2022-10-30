<?php
    getDataFromAPI();

function getDataFromAPI(){
    try{
        $conn = new PDO("mysql:host=localhost;dbname=exchange",
          "root","");
          $curl = curl_init();
    
          curl_setopt_array($curl, [
              CURLOPT_URL => "https://currency-converter-pro1.p.rapidapi.com/convert?from=USD&to=VND&amount=1000",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => [
                  "X-RapidAPI-Host: currency-converter-pro1.p.rapidapi.com",
                  "X-RapidAPI-Key: 732efdcb22mshf8fc683f3f879bfp1a3504jsn276084373c09"
              ],
          ]);
          
          $response = curl_exec($curl);
          $err = curl_error($curl);
          
          $response = json_decode($response);
          
          $from = $response->request->from;
          $to = $response->request->to;
          $amount = $response->request->amount;
          
          $result = (double)$response->result;
          
          $timestamp = $response->meta->timestamp;
          
          curl_close($curl);
          
          if ($err) {
              echo "cURL Error #:" . $err;
          } else {
              $sql = "INSERT INTO currenciesExchange(currency_input,currency_output,result,timestamp,amount) 
                      VALUES('$from','$to','$result','$timestamp','$amount')";
      
              $stm = $conn -> query($sql);
              $n = $stm->rowCount();
              if($n>0){
                echo'<h2>Lấy dữ liệu từ API thành công (nguồn: RapidAPI )</h2>';

                $sql='SELECT * FROM currenciesExchange ORDER BY stt DESC LIMIT 1';
                $stm=$conn->query($sql);
                $data=$stm->fetchAll(PDO::FETCH_ASSOC);
                foreach($data as $item){
                    echo"Tỷ giá hiện tại {$item['currency_input']} -> {$item['currency_output']}: " .number_format($item['result']).' ------------------- cập nhật lúc: ' . date("Y-m-d h:i:s") . ' GMT <hr> <br>';
                }
                echo"<h4 style='color: blue'>Reload để cập nhật giá (giá thay đổi sau 10 phút ) <br></h4>";
                $page = $_SERVER['PHP_SELF'];
                print "<a href=\"$page\">Reload</a>";
              }
              else{
                  echo'<h2>Thao tác thất bại, vui lòng kiểm tra lại câu truy vấn!</h2>';
              }
          
          }
        }
    
        catch(PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }  
};