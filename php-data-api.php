<?php
    /*
    echo '<script> console.log("karthiks edit")</script>';
    $json = json_encode(array(
            'message' => 'Hello World!' 
            ));
    echo $json;
    */
    
    #fwrite(STDOUT, "beginning...");
    
    // Auth token

    $authToken = 'j8s16uw6mzelfz1eases3d8i2d6wuzeq';
    $scoreA = rand (1,100);
    $scoreB = rand (1,100);
    $data_int_url = 'http://data.hasura/v1/query';
    $data_ext_url = 'https://data.barnyard26.hasura-app.io/v1/query';

    // The data to send to the API
    $first_query_postData = array(
        'type' => 'insert',
        'args' => array('table' => 'test-scores', 'objects' => array(array('subjectA' => $scoreA,'subjectB' =>$scoreB)),
        'returning' => array('id'))
    );

    

    // Create the context for the request
    $first_query_context = stream_context_create(array(
        'http' => array(
            // http://www.php.net/manual/en/context.http.php
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n".
            "X-Hasura-User-Id: 1\r\n".
            "X-Hasura-Role: admin\r\n",
              //  "Content-Type: application/json\r\n",
            'content' => json_encode($first_query_postData)
        )
    ));


    // Send the request
    $response = file_get_contents($data_int_url, FALSE, $first_query_context);

    // Check for errors
    if($response === FALSE){
        die('Error');
    }
    else{
        echo "Test scores, $scoreA and $scoreB, have been inserted into table <i>test-scores</i>.<br><br>";
    }
    
    // Decode the response
    $responseData = json_decode($response, TRUE);

    if ($responseData["affected_rows"] > 0) {

        $score_id = $responseData["returning"][0]["id"];
        $score_aggregate = $scoreA + $scoreB;
        
        $second_query_postData = array(
            'type' => 'insert',
            'args' => array('table' => 'score-aggregate', 'objects' => array(array('score-id' => $score_id,'score-aggregate' =>$score_aggregate)))
        );

        $second_query_context = stream_context_create(array(
            'http' => array(
                // http://www.php.net/manual/en/context.http.php
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n".
                "X-Hasura-User-Id: 1\r\n".
                "X-Hasura-Role: admin\r\n",
                'content' => json_encode($second_query_postData)
            )
        ));

        $response = file_get_contents($data_int_url, FALSE, $second_query_context);
        
            // Check for errors
        if($response === FALSE){
            die('Error');
        }
        else{
            echo "An aggregate score of $score_aggregate has been updated in the table <i>score-aggregate</i> for score ID: $score_id";
        }
    }
    else{
        echo "woah";
    }
    
?>
