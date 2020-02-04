<!-- TODO spelling and gramer check -->
<!-- TODO remove header as elaments html tag and doc type as required after completion -->
<!--TODO intagrate database qurry -->


<?php

function generateLineChart_Month(){
    $first = strtotime('first day this month');
    $numOfDays = cal_days_in_month(CAL_GREGORIAN, date('m'), date('y'));
    $days = array();

    for ($i = 1; $i <= $numOfDays; $i++) {
        //list of months
        array_push($days, "day :" . $i);
        //geanarat rand data for display
    }

    //TODO remove once data base it implmented
    //randome genarated grapth (temp)
    $dataTitle ="lineChart_Months";
    $first = strtotime('first day this month');
    $dataLables = array();
    $dataPoints = array();
    $dataAvg = array();

    for ($i = $numOfDays; $i >= 0; $i--) {
        //list of months
        array_push($dataLables, date('D', strtotime("-$i day", $first)));
        //geanarat rand data for display
        array_push($dataPoints, rand(25, 100));
        //a avg of the entire contence of the array
        //todo consider changeing it to look at all past years to provide a more accurit estamit
        $dataPoints = array_filter($dataPoints);
        array_push($dataAvg, array_sum($dataPoints)/count($dataPoints));

    }

    $jsonEncode1 = json_encode($dataLables, JSON_NUMERIC_CHECK);
    $jsonEncode2 = json_encode($dataAvg, JSON_NUMERIC_CHECK);
    $jsonEncode3 = json_encode($dataPoints, JSON_NUMERIC_CHECK);
    $html = <<<pageHTML
    <script type="text/javascript">
        //converts php querry to js for graph
    
        const ctxL = document.getElementById("lineChart_Month").getContext('2d');
        const gradientFill = ctxL.createLinearGradient(0, 0, 0, 350);
        gradientFill.addColorStop(0, "rgba(242,38,19,0.5)");
        gradientFill.addColorStop(1, "rgba(0,230,64,0.5)");
        let LineChartYear = new Chart(ctxL, {
            type: 'line',
            data: {
                labels: $jsonEncode1,
                datasets: [{
                    label: "Expected Usage",
                    data: $jsonEncode2,
                    backgroundColor: [
                        'rgba(0,0,0,0)',
                    ],
                    borderColor: [
                        'rgba(0, 10, 130, .1)',
                    ],
                    borderWidth: ['2'],
                },
                    {
                        label: "Power Used",
                        data: $jsonEncode3,
                        backgroundColor: gradientFill,
                        borderColor: gradientFill,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true
            }
        });
    </script>
pageHTML;
    return $html;


}
//TODO remove once data base it implmented
//randome genarated grapth (temp)

?>