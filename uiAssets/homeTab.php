
<!--Accordion wrapper-->
<div class="accordion md-accordion z-depth-1-half" id="accordionEx194" role="tablist"
     aria-multiselectable="true">

    <hr class="mb-0">

    <?php

    //TODO FIND OUT WHAT ELCE SHOULD BE IMPLMENTED
    //TODO implment lineChart.php after completing it
    //TODO enable chate to apper more than once
    //todo a querry to cheack how meny hose some has
    $x = 1;
    //for building more than one chart


    while ($x <= 1) {
        //TODO fix drop dowp fucntion
        echo '
        <!-- Accordion card -->
          <div class="card">
          <!-- Card header -->
                <div class="card-header" role="tab" id="heading' . $x . '">
                  <a data-toggle="collapse" data-parent="#accordionEx194" href="#collapse' . $x . '" aria-expanded="true"
                    aria-controls="collapse4">
                    <h3 class="mb-0 mt-3 red-text">
                      My Home <i class="fas fa-angle-down rotate-icon fa-2x"></i>
                    </h3>
                  </a>
                </div>
          <!-- Card body -->
            <div id="collapse' . $x . '" class="collapse show" role="tabpanel" aria-labelledby="heading' . $x . '"
              data-parent="#accordionEx194">
                  <div class="card-body pt-0">
                  
                  
                  <div class="flex-sm-row justify-content-center">     
                      <!--Grid column-->
                        <!--Date select
                        <p class="lead align-content-center">
                          <span class="badge info-color-dark p-2">Date range</span>
                        </p>-->
                        <form id="myForm">                        
                            <div class="select-area card">
                                <select name="choose" id="choose" class="input-select">
                                    <option value="nul" selected>date range</option>
                                    <option value="opt1">year</option>
                                    <option value="opt2">month</option>
                                    <option value="opt3">day</option>
                                    </select>
                            </div>
                       
                        </form>
                  </div>
                 <!--TODO intagrate database qurry -->
                 
                    <section class="jqueryOptions opt1">
                        <canvas id="lineChart_Year"></canvas>
                    </section>
                    <section class="jqueryOptions opt2">
                        <canvas id="lineChart_Month"></canvas>
                    </section>
                    
                    <section class="jqueryOptions opt3">
                        <canvas id="lineChart_Day"></canvas>
                    </section>
                       
                  </div>
                  
            </div>
        </div>
      <!-- Accordion card -->
      ';//echo end
        $x++;//+1 to x for steting
    }
    ?>
</div>


<!--/.Accordion wrapper-->

<script>
    $(function() {
        $('.jqueryOptions').hide();

        $('#choose').change(function () {
            $('.jqueryOptions').slideUp();
            $('.jqueryOptions').removeClass('current-opt');
            $("." + $(this).val()).slideDown();
            $("." + $(this).val()).addClass('current-opt');
        });
    });
</script>

<?php
//require "charts/lineChart_Year.php";
//require "charts/lineChart_Month.php";
require "charts/lineChart_Day.php";
?>