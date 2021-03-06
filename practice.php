<?php
	require_once'header.php';
	
	if (!$loggedin) die();
	
	//echo "(Coming soon)";
	
	$result2 = queryMysql("SELECT *FROM vocab_user WHERE USER_ID='$user_id' ORDER BY added_date ");
	$num = $result2->num_rows;
	
	for ($j=0;$j<$num;++$j)
	{
		$row = $result2->fetch_array(MYSQLI_ASSOC);
		$words[$j]=$row['USER_WORD_ID'];
		$regular_index[$j] = $row['REGULARITY_INDEX'];
		$retention_rate[$j] = $row['retention_rate'];
		$learning_progress[$j] = $row['learning_progress'];
		//echo $words[$j]."<br>";
		//echo $j."<br>";
	}
	$continue = true; //false when all retention_rate>95%
	$j-=1;
	for ($j=$j;$j>=0;--$j)
	{	

		if (isset($_GET['difficulty']))
		$result3 = queryMysql("SELECT *FROM vocabulary WHERE WORD_ID = '$words[$j]' ORDER BY ReliablePoint");
		else $result3 = queryMysql("SELECT *FROM vocabulary WHERE WORD_ID = '$words[$j]' ");
		$row  = $result3->fetch_array(MYSQLI_ASSOC);
		$idword[$j] = $row['ID'];
		$word[$j] = $row['WORDS'];
		if ($row['pronunciation']!="") $pronunciation[$j] = $row['pronunciation'];
		else $pronunciation[$j]=null;
		$wordforms[$j] = $row['WORDFORMS'];
		if ($row['TYPES']!="")$types[$j] = $row['TYPES'];
		else $types[$j]="";
		if ($row['USES']!="") $uses[$j] = $row['USES'];
		else $uses[$j]="";
		$meanings[$j] = $row['MEANING'];
		$adder[$j] = $row['AddedBY'];
		$time[$j]= $row['dateCreated'];
		$reliablepoint[$j] = $row['ReliablePoint'];
		$examples[$j] = $row['example'];
	}
	
	
	if (isset($_POST['start']))
	{
	$pos = rand(0,$num);
	/*while($continue){
	
	//echo $pos;
	
	}*/
	echo $num;
	}
	
	echo '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quiz</title>
    <!-- jquery for maximum compatibility -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>

    var quiztitle = "Multiple choice question";

    /**
    * Set the information about your questions here. The correct answer string needs to match
    * the correct choice exactly, as it does string matching. (case sensitive)
    *
    */
    var quiz = [
        {
            "question"      :   "Who came up with the theory of relativity?",
            //"image"         :   "",
            "choices"       :   [
                                    "Sir Isaac Newton",
                                    "Nicolaus Copernicus",
                                    "Albert Einstein",
                                    "Ralph Waldo Emmerson"
                                ],
            "correct"       :   "Albert Einstein",
            //"explanation"   :   "Albert Einstein drafted the special theory of relativity in 1905.",
        },
        {
            "question"      :   "Who is on the two dollar bill?",
            "image"         :   "",
            "choices"       :   [
                                    "Thomas Jefferson",
                                    "Dwight D. Eisenhower",
                                    "Benjamin Franklin",
                                    "Abraham Lincoln"
                                ],
            "correct"       :   "Thomas Jefferson",
            "explanation"   :   "The two dollar bill is seldom seen in circulation. As a result, some businesses are confused when presented with the note.",
        },
        {
            "question"      :   "What event began on April 12, 1861?",
            "image"         :   "",
            "choices"       :   [
                                    "First manned flight",
                                    "California became a state",
                                    "American Civil War began",
                                    "Declaration of Independence"
                                ],
            "correct"       :   "American Civil War began",
            "explanation"   :   "South Carolina came under attack when Confederate soldiers attacked Fort Sumter. The war lasted until April 9th 1865.",
        },
		{
            "question"      :   "Who am I?",
            "image"         :   "",
            "choices"       :   [
                                    "Thomas ",
                                    "Dwight ",
                                    "Benjamin ",
                                    "Abraham "
                                ],
            "correct"       :   "Thomas ",
            "explanation"   :   "The two dollar bill is seldom seen in circulation. As a result, some businesses are confused when presented with the note.",
        },

    ];


    /******* No need to edit below this line *********/
    var currentquestion = 0, score = 0, submt=true, picked;

    jQuery(document).ready(function($){

        /**
         * HTML Encoding function for alt tags and attributes to prevent messy
         * data appearing inside tag attributes.
         */
        function htmlEncode(value){
          return $(document.createElement("div")).text(value).html();
        }

        /**
         * This will add the individual choices for each question to the ul#choice-block
         *
         * @param {choices} array The choices from each question
         */
        function addChoices(choices){
            if(typeof choices !== "undefined" && $.type(choices) == "array"){
                $("#choice-block").empty();
                for(var i=0;i<choices.length; i++){
                    $(document.createElement("li")).addClass("choice choice-box").attr("data-index", i).text(choices[i]).appendTo("#choice-block");                    
                }
            }
        }
        
        /**
         * Resets all of the fields to prepare for next question
         */
        function nextQuestion(){
            submt = true;
            $("#explanation").empty();
            $("#question").text(quiz[currentquestion]["question"]);
            $("#pager").text("Question " + Number(currentquestion + 1) + " of " + quiz.length);
            /*These for processing images
			if(quiz[currentquestion].hasOwnProperty("image") && quiz[currentquestion]["image"] != ""){
                if($("#question-image").length == 0){
                    $(document.createElement("img")).addClass("question-image").attr("id", "question-image").attr("src", quiz[currentquestion]["image"]).attr("alt", htmlEncode(quiz[currentquestion]["question"])).insertAfter("#question");
                } else {
                    $("#question-image").attr("src", quiz[currentquestion]["image"]).attr("alt", htmlEncode(quiz[currentquestion]["question"]));
                }
            } else {
                $("#question-image").remove();
            }
			*/
            addChoices(quiz[currentquestion]["choices"]);
            setupButtons();
        }

        /**
         * After a selection is submitted, checks if its the right answer
         *
         * @param {choice} number The li zero-based index of the choice picked
         */
		 
        function processQuestion(choice){
            if(quiz[currentquestion]["choices"][choice] == quiz[currentquestion]["correct"]){
                $(".choice").eq(choice).css({"background-color":"#50D943"});
                $("#explanation").html("<strong>Correct!</strong> " + htmlEncode(quiz[currentquestion]["explanation"]));
                score++;
            } else {
                $(".choice").eq(choice).css({"background-color":"#D92623"});
                $("#explanation").html("<strong>Incorrect.</strong> " + htmlEncode(quiz[currentquestion]["explanation"]));
            }
            currentquestion++;
            $("#submitbutton").html("NEXT QUESTION &raquo;").on("click", function(){
                if(currentquestion == quiz.length){
                    endQuiz();
                } else {
                    $(this).text("Check Answer").css({"color":"yellow","background-color":"#1a1aff"}).off("click");
                    nextQuestion();
                }
            })
        }

        /**
         * Sets up the event listeners for each button.
         */
        function setupButtons(){
            $(".choice").on("mouseover", function(){
                $(this).css({"background-color":"#e1e1e1","border":"none"});
            });
            $(".choice").on("mouseout", function(){
                $(this).css({"background-color":"#fff","border":"1px solid black"});
            })
            $(".choice").on("click", function(){
                picked = $(this).attr("data-index");
                $(".choice").removeAttr("style").off("mouseout mouseover");
                $(this).css({"border-color":"#222","font-weight":700,"background-color":"#c1c1c1"});
                if(submt){
                    submt=false;
                    $("#submitbutton").css({"color":"#000"}).on("click", function(){
                        $(".choice").off("click");
                        $(this).off("click");
                        processQuestion(picked);
                    });
                }
            })
        }
        
        /**
         * Quiz ends, display a message.
         */
        function endQuiz(){
            $("#explanation").empty();
            $("#question").empty();
            $("#choice-block").empty();
            $("#submitbutton").remove();
            $("#question").text("You got " + score + " out of " + quiz.length + " correct.");
            $(document.createElement("h2")).css({"text-align":"center", "font-size":"4em"}).text(Math.round(score/quiz.length * 100) + "%").insertAfter("#question");
        }

        /**
         * Runs the first time and creates all of the elements for the quiz
         */
        function init(){
            //add title
            if(typeof quiztitle !== "undefined" && $.type(quiztitle) === "string"){
                $(document.createElement("h1")).text(quiztitle).appendTo("#frame");
            } else {
                $(document.createElement("h1")).text("Quiz").appendTo("#frame");
            }

            //add pager and questions
            if(typeof quiz !== "undefined" && $.type(quiz) === "array"){
                //add pager
                $(document.createElement("p")).addClass("pager").attr("id","pager").text("Question 1 of " + quiz.length).appendTo("#frame");
                //add first question
                $(document.createElement("h2")).addClass("question").attr("id", "question").text(quiz[0]["question"]).appendTo("#frame");
                //add image if present
                if(quiz[0].hasOwnProperty("image") && quiz[0]["image"] != ""){
                    $(document.createElement("img")).addClass("question-image").attr("id", "question-image").attr("src", quiz[0]["image"]).attr("alt", htmlEncode(quiz[0]["question"])).appendTo("#frame");
                }
                $(document.createElement("p")).addClass("explanation").attr("id","explanation").html("&nbsp;").appendTo("#frame");
            
                //questions holder
                $(document.createElement("ul")).attr("id", "choice-block").appendTo("#frame");
            
                //add choices
                addChoices(quiz[0]["choices"]);
            
                //add submit button
                $(document.createElement("div")).addClass("choice-box").attr("id", "submitbutton").text("Check Answer").css({"font-weight":700,"color":"yellow","padding":"30px 0"}).appendTo("#frame");
            
                setupButtons();
            }
        }
        
        init();
    });
    </script>
    <style type="text/css" media="all">
        /*css reset */
        //html,body,div,span,h1,h2,h3,h4,h5,h6,p,code,small,strike,strong,sub,sup,b,u,i{border:0;font-size:100%;font:inherit;vertical-align:baseline;margin:0;padding:0;} 
		
        article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block;} 
		
        //body{line-height:1; font:normal 0.9em/1em "Helvetica Neue", Helvetica, Arial, sans-serif;} 
        ol,ul{list-style:none;}
        strong{font-weight:700;}
        #frame{max-width:620px;width:auto;border:1px solid #ccc;background:#fff;padding:10px;margin:3px;}
        h1{font:normal bold 2em/1.8em "Helvetica Neue", Helvetica, Arial, sans-serif;text-align:left;border-bottom:1px solid #999;padding:0;width:auto}
        h2{font:italic bold 1.3em/1.2em "Helvetica Neue", Helvetica, Arial, sans-serif;padding:0;text-align:center;margin:20px 0;}
        p.pager{margin:5px 0 5px; font:bold 1em/1em "Helvetica Neue", Helvetica, Arial, sans-serif;color:#999;}
        
        #choice-block{display:block;list-style:none;margin:0;padding:0;}
        #submitbutton{background:#5a6b8c;}
        #submitbutton:hover{background:#1a1aff;color:yellow;}
        #explanation{margin:0 auto;padding:20px;width:75%;}
        .choice-box{display:block;text-align:center;margin:8px auto;padding:10px 0;border:1px solid #666;cursor:pointer;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;}
    </style>
</head>
<body>
    <div id="frame" role="content"></div>
</body>
</html>
';

?>