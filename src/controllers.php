<?php

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Goutte\Client;
use Ghunti\HighchartsPHP\Highchart;
use Ghunti\HighchartsPHP\HighchartJsExpr;
use Silex\Provider\SerializerServiceProvider;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

//HOMEPAGE
$app->match('/', function () use ($app) {
    $em = $app['orm.em'];
    
    $q = $em->createQuery("select p from \Dev\Pub\Entity\Project p where p.start_date < CURRENT_TIMESTAMP() and p.end_date > CURRENT_TIMESTAMP()");
    $currentProjects = $q->getResult();

    $currentProjectsArray = array();
    $futureProjectsArray = array();
    $pastProjectsArray = array();

    foreach ($currentProjects as $project) {
        $projectName = $project->getName();
        $projectId = $project->getId();
        $url = $app['url_generator']->generate('projectResults', array('id' => $projectId));
        $currentProjectsArray[] = array($projectName, $url);
    }

    $q = $em->createQuery("select p from \Dev\Pub\Entity\Project p where p.end_date < CURRENT_TIMESTAMP()")->setMaxResults(5);

    $pastProjects = $q->getResult();


    foreach ($pastProjects as $project) {
        $projectName = $project->getName();
        $projectId = $project->getId();
        $url = $app['url_generator']->generate('projectResults', array('id' => $projectId));
        $pastProjectsArray[] = array($projectName, $url);
    }
    
    $q = $em->createQuery("select p from \Dev\Pub\Entity\Project p where p.start_date > CURRENT_TIMESTAMP() ORDER BY p.start_date DESC")->setMaxResults(5);
    $futureProjects = $q->getResult();

    foreach ($futureProjects as $project) {
        $projectName = $project->getName();
        $projectId = $project->getId();
        $url = $app['url_generator']->generate('projectResults', array('id' => $projectId));
        $projectStartDate = $project->getStartDate();
        $futureProjectsArray[] = array($projectName, $url);
    }
    return $app['twig']->render('index.html.twig', array('currentProjects' => $currentProjectsArray, 'pastProjects'=>$pastProjectsArray, 'futureProjects'=>$futureProjectsArray));

})->bind('homepage');


//LOGIN
$app->match('/login', function (Request $request) use ($app) {
    $form = $app['form.factory']->createBuilder('form')
        ->add(
            'username',
            'text',
            array(
                'label' => 'Username',
                'data' => $app['session']->get('_security.last_username')
            )
        )
        ->add('password', 'password', array('label' => 'Password'))
        ->getForm()
    ;

    return $app['twig']->render('login.html.twig', array(
        'form'  => $form->createView(),
        'error' => $app['security.last_error']($request),
    ));
})->bind('login');


//NUEVO PROYECTO
$app->match('/newProject', function(Request $request) use ($app){
    $em = $app['orm.em'];
    $builder = $app['form.factory']->createBuilder('form');

    $form = $builder
        ->add('name', 'text', array('label' => 'Nombre del proyecto', 'required'=>true, 'empty_data'  => null))
        ->add('startDate', 'text', array('label' => 'Fecha y hora de inicio', 'required'=>true, 'empty_data'  => null))
        ->add('duration', 'number', array('label' => 'Duración (horas)', 'required'=>true, 'empty_data'  => null))
        ->add(
            $builder->create('Keywords', 'form')
                ->add('keyword1', 'text', array('label' => 'Keyword 1', 'required'=>true, 'empty_data'  => null))
                ->add('keyword2', 'text', array('label' => 'Keyword 2', 'required'=>false, 'empty_data'  => null))
                ->add('keyword3', 'text', array('label' => 'Keyword 3', 'required'=>false, 'empty_data'  => null))
                ->add('keyword4', 'text', array('label' => 'Keyword 4', 'required'=>false, 'empty_data'  => null))
        )
        ->add('create', 'submit', array('label' => 'Crear Proyecto'))
        ->getForm();

    $form->handleRequest($request);
    
    if($form->isSubmitted()){
        if($form->isValid()){
            $data = $form->getData();
            if($data['name'] != NULL && $data['startDate'] != NULL && $data['duration']!=NULL && $data['Keywords']['keyword1']!=NULL){
                $project = new \Dev\Pub\Entity\Project();
                $project->setName($data['name']);
                $startDate = $data['startDate'];
                $startDateTime = strtotime($startDate);
                $startDate = new \DateTime($startDate);
                $project->setStartDate($startDate);
                $endDateTime = strtotime(date('Y-m-d H:i:s', strtotime("+{$data['duration']}  hours", $startDateTime)));
                $endDate = date('Y-m-d H:i:s', $endDateTime);
                $endDate = new \DateTime($endDate);
                $project->setEndDate($endDate);

                $keywordsRepository = $em->getRepository('\Dev\Pub\Entity\Keyword');
                foreach ($data['Keywords'] as $keyword => $value) {
                    if($value != NULL){
                        $tempKeyword = new \Dev\Pub\Entity\keyword();
                        $tempKeyword->setName($value);
                        $project->addKeyword($tempKeyword);
                    }
                }

                $em->persist($project);
                $em->flush();
                $url = $app['url_generator']->generate('projectResults', array('id' => $project->getId()));
                return $app->redirect($url);
            }else{
                //TODO do something...
            }
        }
    }
    return $app['twig']->render('newproject.html.twig', array('form' => $form->createView()));
})->bind('newProject');


//AÑADIR KEYWORDS
$app->match('/addKeywords/{projectId}', function(Request $request, $projectId) use ($app){
    $em = $app['orm.em'];

    $projectsRepository = $em->getRepository('\Dev\Pub\Entity\Project');
    $project = $projectsRepository->findOneBy(array('id' => $projectId));

    //solo se pueden añadir keywords en proyectos en ejecución o futuros, por lo lo que comprobamos si este proyecto es elegible
    $homeData = array('name'=>"Home", 'url'=> $app['url_generator']->generate('homepage'));
    $projectData = array(
        'name'=>$project->getName(), 
        'url'=> $app['url_generator']->generate('projectResults', array('id' => $project->getId()))
    );

    $now = new \DateTime();
    if($project->getEndDate() > $now){
        $keywords = $project->getKeywords();
        $currentKeywords = array();
        foreach ($keywords as $keyword) {
            $currentKeywords[] = $keyword->getName();
        }
        $data = array();
        $form = $app['form.factory']->createBuilder('form', $data)
            ->add('keyword1', 'text')
            ->add('keyword2', 'text', array('required'=>false, 'empty_data'  => null))
            ->add('keyword3', 'text', array('required'=>false, 'empty_data'  => null))
            ->add('keyword4', 'text', array('required'=>false, 'empty_data'  => null))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            foreach ($data as $keyword) {
                if($keyword != NULL){
                    $newKeyword = new \Dev\Pub\Entity\Keyword();
                    $newKeyword->setName($keyword);
                    $newKeyword->setProject($project);
                    $project->addKeyword($newKeyword);
                    $em->persist($newKeyword);
                    $em->persist($project);
                }
            }
            $em->flush();
            return $app->redirect($request->getRequestUri());
        } 
        return $app['twig']->render('addkeywords.html.twig', array(
            'currentKeywords'=>$currentKeywords, 
            'form' => $form->createView(),
            'home' => $homeData,
            'project' => $projectData,
        ));
    }

    return $app['twig']->render('addkeywords.html.twig', array(
        'form' => "El proyecto ya ha finalizado.",
        'home' => $homeData,
        'project' => $projectData,
    ));
})->bind('addKeywords');

//VER RESULTADOS PROYECTO+KEYWORD+SITE
$app->match('/projectResults/{projectId}/{keywordId}/{siteName}', function(Request $request, $projectId, $keywordId, $siteName) use ($app){
    $em = $app['orm.em'];
    $keywordsRepository = $em->getRepository('\Dev\Pub\Entity\Keyword');
    $keyword = $keywordsRepository->findOneBy(array('id'=>$keywordId));
    $serps = $keyword->getSerps();
    $headLineReportRows = array();
    $updatedTimeReportRows = array();
    $serpCount = 0;
    $actualCount = 0;


    foreach ($serps as $serp) {
        $serpResults = $serp->getSerpResults();
        $serpCount++;
        foreach ($serpResults as $serpResult) {
            $site = $serpResult->getSite();
            $type = $serpResult->getType();
            $date = $serp->getTimestamp();
            $printDate = date_format($date, 'H:i:s');

            if($type == "news" && $site == $siteName){
                $actualCount++;

                //si no es el primer elemento del array, comparamos con el anterior
                if($headLineReportRows != 0){
                    //si el elemento anterior no es vacío (...), pasamos a comprarar cada elemento
                    $headLineReportRows[] = array(
                        'date' => array($printDate, true),
                        'url' => array($serpResult->getUrl(), false),
                        'ranking' => array($serpResult->getSubrank(),false),
                        'newstitle' => array($serpResult->getTitle(),false),
                        'urltitle' => array($serpResult->getURLTitle(), false),
                        'urlh1' => array($serpResult->getUrlH1(), false),
                    );

                    if(sizeof($headLineReportRows)>1){
                        //Tenemos mínimo 2 elementos

                        $i=sizeof($headLineReportRows)-2;
                        //comprobamos si elemento anterior es vacío
                        if($headLineReportRows[$i]['ranking'][0]=="..."){
                            //si es así, vamos hacia atrás hasta encontrar un elemento con contenido.
                            $i--;
                            while($i >= 0 && $headLineReportRows[$i]['ranking'][0] =="..."){
                                $i--;
                            }
                            //Aquí, o tenemos el índice del elemento con contenido, o tenemos -1
                        }
                        if($i != -1) {
                            //Si no es -1, quiere decir que encontramos algo con lo que comparar, ya sea el elemento anterior o uno más atrás
                            foreach ($headLineReportRows[$i] as $elementIndex => $elementValue) {
                                if($elementValue[0] != $headLineReportRows[sizeof($headLineReportRows)-1][$elementIndex][0]){
                                    $headLineReportRows[sizeof($headLineReportRows)-1][$elementIndex][1] = true;
                                }
                            }
                        }
                    }

                    $updatedTimeReportRows[] = array(
                        'date' => array($printDate,true),
                        'url' => array($serpResult->getUrl(),false),
                        'ranking' => array($serpResult->getSubrank(),false),
                        'title' => array($serpResult->getTitle(),false),
                        'urldate' => array($serpResult->getURLDate(),false),
                        'urldateissued' => array($serpResult->getURLDateIssued(),false),
                        'urltextdate' => array($serpResult->getURLTextDate(),false),
                    );

                    if(sizeof($updatedTimeReportRows)>1){
                        //Tenemos mínimo 2 elementos

                        $i=sizeof($updatedTimeReportRows)-2;
                        //comprobamos si elemento anterior es vacío
                        if($updatedTimeReportRows[$i]['ranking'][0]=="..."){
                            //si es así, vamos hacia atrás hasta encontrar un elemento con contenido.
                            $i--;
                            while($i >= 0 && $updatedTimeReportRows[$i]['ranking'][0] =="..."){
                                $i--;
                            }
                            //Aquí, o tenemos el índice del elemento con contenido, o tenemos -1d
                        }
                        if($i != -1) {
                            //Si no es -1, quiere decir que encontramos algo con lo que comparar, ya sea el elemento anterior o uno más atrás
                            foreach ($updatedTimeReportRows[$i] as $elementIndex => $elementValue) {
                                if($elementValue[0] != $updatedTimeReportRows[sizeof($updatedTimeReportRows)-1][$elementIndex][0]){
                                    $updatedTimeReportRows[sizeof($updatedTimeReportRows)-1][$elementIndex][1] = true;
                                }
                            }
                        }
                    }


                    $socialReportRows[] = array(
                        'date' => array($printDate,true),
                        'url' => array($serpResult->getUrl(),false),
                        'ranking' => array($serpResult->getSubrank(),false),
                        'tweets' => array($serpResult->getUrlTweetCount(),false),
                        'fblikes' => array($serpResult->getUrlFbLikeCount(),false),
                        'fbshares' => array($serpResult->getUrlFbShareCount(),false),
                        'fbtotal' => array($serpResult->getUrlFbTotalCount(),false),
                        'plusone' => array($serpResult->getUrlPlusOneCount(),false),
                    );

                    if(sizeof($socialReportRows)>1){
                        //Tenemos mínimo 2 elementos

                        $i=sizeof($socialReportRows)-2;
                        //comprobamos si elemento anterior es vacío
                        if($socialReportRows[$i]['ranking'][0]=="..."){
                            //si es así, vamos hacia atrás hasta encontrar un elemento con contenido.
                            $i--;
                            while($i >= 0 && $socialReportRows[$i]['ranking'][0] =="..."){
                                $i--;
                            }
                            //Aquí, o tenemos el índice del elemento con contenido, o tenemos -1d
                        }
                        if($i != -1) {
                            //Si no es -1, quiere decir que encontramos algo con lo que comparar, ya sea el elemento anterior o uno más atrás
                            foreach ($socialReportRows[$i] as $elementIndex => $elementValue) {
                                if($elementValue[0] != $socialReportRows[sizeof($socialReportRows)-1][$elementIndex][0]){
                                    $socialReportRows[sizeof($socialReportRows)-1][$elementIndex][1] = true;
                                }
                            }
                        }
                    }
                }
            }else if($serpCount > $actualCount){
                $headLineReportRows[] = array(
                    'date' => array("...", true),
                    'url' => array("...", false),
                    'ranking' => array("...",false),
                    'newstitle' => array("...",false),
                    'urltitle' => array("...", false),
                    'urlh1' => array("...", false)
                );

                $updatedTimeReportRows[] = array(
                    'date' => array("...",false),
                    'url' => array("...",false),
                    'ranking' => array("...",false),
                    'title' => array("...",false),
                    'urldate' => array("...",false),
                    'urldateissued' => array("...",false),
                    'urltextdate' => array("...",false),
                );

                $socialReportRows[] = array(
                    'date' => array("...",true),
                    'url' => array("...",false),
                    'ranking' => array("...",false),
                    'tweets' => array("...",false),
                    'fblikes' => array("...",false),
                    'fbshares' => array("...",false),
                    'fbtotal' => array("...",false),
                    'plusone' => array("...",false),
                );
                $actualCount=$serpCount;
            }
        }
    }

    $headLineReportColumns = array(
        'date' => "hora",
        'url' => "url",
        'ranking' => "ranking",
        'newstitle' => "title news",
        'urltitle' => "title noticia",
        'urlh1' => "h1 noticia",
    );
    $updatedTimeReportColumns = array(
        'date' => "hora",
        'url' => "url",
        'ranking' => "ranking",
        'title' => "title news",
        'urldate' => "etiqueta Date",
        'urldateissued' => "etiqueta Date.issued",
        'urltextdate' => "date en texto",
    );
    $socialReportColumns = array(
        'date' => "hora",
        'url' => "url",
        'ranking' => "ranking",
        'tweets' => "tweets",
        'fblikes' => "Likes Fb",
        'fbshares' => "Shares FB",
        'fbtotal' => "Total FB",
        'plusone' => "+1s",
    );

    $homeData = array('name'=>"Home", 'url'=> $app['url_generator']->generate('homepage'));
    $projectsRepository = $em->getRepository('\Dev\Pub\Entity\Project');
    $project = $projectsRepository->findOneBy(array('id'=>$projectId));
    $projectData = array(
        'name'=>$project->getName(), 
        'url'=> $app['url_generator']->generate('projectResults', array('id' => $project->getId()))
    );
    $keywordData = array(
        'name'=>$keyword->getName(), 
        'url'=> $app['url_generator']->generate('projectResultsKeyword', array(
            'projectId' => $project->getId(), 
            'keywordId'=>$keyword->getId()))
    );
    $siteData = array('name' => $siteName);

    return $app['twig']
        ->render('projectKeywordSiteResults.html.twig', 
        array(
            'headLineReportColumns'=>$headLineReportColumns, 
            'headLineReportRows'=>$headLineReportRows,
            'updatedTimeReportRows'=>$updatedTimeReportRows,
            'updatedTimeReportColumns'=>$updatedTimeReportColumns,
            'socialReportRows'=>$socialReportRows,
            'socialReportColumns'=>$socialReportColumns,
            'home' => $homeData,
            'project' => $projectData,
            'keyword' =>$keywordData,
            'site' => $siteData
        )
    );
})->bind('projectResultsKeywordSite');

//VER RESULTADOS PROYECTO+KEYWORD
$app->match('/projectResults/{projectId}/{keywordId}', function(Request $request, $projectId, $keywordId) use ($app){
    $em = $app['orm.em'];
    $projectsRepository = $em->getRepository('\Dev\Pub\Entity\Project');
    $project = $projectsRepository->findOneBy(array('id' => $projectId));
    $keywords = $project->getKeywords();

    $otherKeywords = array();
    foreach ($keywords as $keyword) {
        $url = $app['url_generator']->generate('projectResultsKeyword', array('projectId' => $projectId, 'keywordId' => $keyword->getId()));
        $otherKeywords[] = array($keyword->getName(), $url);
    }

    $keywordsRepository = $em->getRepository('\Dev\Pub\Entity\Keyword');
    $keyword = $keywordsRepository->findOneBy(array('id' => $keywordId));

    $homeData = array('name'=>"Home", 'url'=> $app['url_generator']->generate('homepage'));
    $projectData = array(
        'name'=>$project->getName(), 
        'url'=> $app['url_generator']->generate('projectResults', array('id' => $project->getId()))
    );
    $keywordData = array(
        'name'=>$keyword->getName(), 
        'url'=> $app['url_generator']->generate('projectResultsKeyword', array(
            'projectId' => $project->getId(), 
            'keywordId'=>$keyword->getId()))
    );

    $serps = $keyword->getSerps();
    $i=0;
    $serpNumber=0;



    $visibilityArray = array();
    $rankingArray = array();
    $scrappedTimes = array();
    $htmlSerps = array();
    
    foreach ($serps as $serp) { 
        //recorremos serps               
        $serpresults = $serp->getSerpResults();
        $scrappedTime = date_format($serp->getTimestamp(), 'Y-m-d H:i:s');
        $scrappedTimes[] = $scrappedTime;
        $htmlSerps[] = array($scrappedTime => unserialize($serp->getNewsHtml()));
        foreach ($serpresults as $serpresult) {
            //recorremos cada resultado
            if($serpresult->getType()=="news"){
                //nos quedamos sólo con los del tipo "news"
                $currentSite = $serpresult->getSite();
                if(array_key_exists($currentSite, $rankingArray)){
                    //No es la primera vez
                    if(sizeof($rankingArray[$currentSite])<$serpNumber){
                        //hay que rellenar hasta la fecha actual
                        for($temp=sizeof($rankingArray[$currentSite]); $temp<($serpNumber); $temp++){
                            //por cada hueco añadimos un null
                            $rankingArray[$currentSite][] = array($scrappedTimes[$temp] => null);
                            $visibilityArray[$currentSite][] = array($scrappedTimes[$temp] => end($visibilityArray[$currentSite][$temp-1]));
                        }
                        //al terminar añadimos el valor actual
                        $rankingArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $serpresult->getSubrank());
                        switch ($serpresult->getSubrank()) {
                            case 1:
                                $currentVisibility = end($visibilityArray[$currentSite][$serpNumber-1]) + $app['visibilityFirst'];
                                break;
                            case 2:
                                $currentVisibility = end($visibilityArray[$currentSite][$serpNumber-1]) + $app['visibilitySecond'];
                                break;
                            case 3:
                                $currentVisibility = end($visibilityArray[$currentSite][$serpNumber-1]) + $app['visibilityThird'];
                                break;
                            default:
                                $currentVisibility = 0;
                                break;
                        }
                        $visibilityArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $currentVisibility);
                    }
                    else if(sizeof($rankingArray[$currentSite]) == $serpNumber){
                        //es el valor que toca, no hay que rellenar
                        $rankingArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $serpresult->getSubrank());
                        switch ($serpresult->getSubrank()) {
                            case 1:
                                $currentVisibility = end($visibilityArray[$currentSite][$serpNumber-1]) + $app['visibilityFirst'];
                                break;
                            case 2:
                                $currentVisibility = end($visibilityArray[$currentSite][$serpNumber-1]) + $app['visibilitySecond'];
                                break;
                            case 3:
                                $currentVisibility = end($visibilityArray[$currentSite][$serpNumber-1]) + $app['visibilityThird'];
                                break;
                            default:
                                $currentVisibility = 0;
                                break;
                        }
                        $visibilityArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $currentVisibility);
                    }                    
                }else{
                    //es la primera vez que aparece el site así que simplemente asignamos los valores
                    for($temp=0;$temp<$serpNumber;$temp++){
                        //rellenamos con null hasta la fecha actual, si procede
                        $rankingArray[$currentSite][] = array($scrappedTimes[$temp] => null);
                        $visibilityArray[$currentSite][] = array($scrappedTimes[$temp] => null);
                    }
                    $rankingArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $serpresult->getSubrank());
                    switch ($serpresult->getSubrank()) {
                        case 1:
                            $currentVisibility = $app['visibilityFirst'];
                            break;
                        case 2:
                            $currentVisibility = $app['visibilitySecond'];
                            break;
                        case 3:
                            $currentVisibility = $app['visibilityThird'];
                            break;
                        default:
                            $currentVisibility = 0;
                            break;
                    }
                    $visibilityArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $currentVisibility);
                }
            }   
            $i++;
        }
      
        foreach ($rankingArray as $key => $rankingDomain) {
            //cuantos elementos tiene el array?
            $temp = sizeof($rankingDomain);
            //si el número de elementos es menor que la cantidad de serp que llevamos, rellenamos con null
            
            if($temp<($serpNumber+1)){
                for($temp;$temp<($serpNumber+1);$temp++){
                    $rankingArray[$key][] = array($scrappedTimes[$temp] => null);
                }
            }
        }

        foreach ($visibilityArray as $key => $visibilityDomain) {
            //cuantos elementos tiene el array?
            $temp = sizeof($visibilityDomain);
            //si el número de elementos es menor que la cantidad de serp que llevamos, rellenamos con null
            
            if($temp<($serpNumber+1)){
                for($temp;$temp<($serpNumber+1);$temp++){
                    $visibilityArray[$key][] = array($scrappedTimes[$temp] => end($visibilityArray[$key][$temp-1]));
                }
            }
        }

        $serpNumber++;
    }

    if(!empty($rankingArray)){
        foreach ($rankingArray as $key => $value) {
            break;
        }
        $currentIndex = 0;
        $dataRankingTemp = array();
        $dataVisibilityTemp = array();

        for($x=0;$x<sizeof($rankingArray[$key]);$x++){
            $currentTime = $scrappedTimes[$x];
            $dataRankingTemp[] = array($scrappedTimes[$x]);
            array_push($dataRankingTemp[$currentIndex], $htmlSerps[$x][$currentTime]);
            $dataVisibilityTemp[] = array($scrappedTimes[$x]);
            foreach ($rankingArray as $site => $rank) {
                array_push($dataRankingTemp[$currentIndex], $rank[$x][$currentTime]);
                array_push($dataVisibilityTemp[$currentIndex], $visibilityArray[$site][$x][$currentTime]);
            }
            $currentIndex++;
        }

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        $dataRankingTemp = $serializer->serialize($dataRankingTemp, 'json');
        $dataVisibilityTemp = $serializer->serialize($dataVisibilityTemp, 'json');
        $columns = array('Minuto');
        foreach ($rankingArray as $site => $rankings) {
            $columns[] = $site;
        }
        $columns = $serializer->serialize($columns, 'json');
        $sites = array();
        foreach ($rankingArray as $site => $rank){
            $url = $app['url_generator']->generate('projectResultsKeywordSite', array(
                'projectId' => $project->getId(),
                'keywordId' => $keyword->getId(),
                'siteName' => $site
            ));
            $sites[] = array(
                'name' => $site,
                'url' => $url
            );
        }

        return $app['twig']->render('projectresults.html.twig', array(
            'dataRanking' => $dataRankingTemp, 
            'dataVisibility' => $dataVisibilityTemp, 
            'columns'=>$columns,
            'keyword'=>$keywordData,
            'project' => $projectData,
            'home' => $homeData,
            'otherKeywords' => $otherKeywords,
            'sites' => $sites,
        ));
    }
    

    
    $data = array();

    return $app['twig']->render('projectresults.html.twig', array(
        'dataRanking' => $data, 
        'dataVisibility'=> $data, 
        'keyword'=>$keywordData,
        'project' => $projectData,
        'home' => $homeData,
        'sites' => null,
        'otherKeywords' => $otherKeywords));
})->bind('projectResultsKeyword');

//VER RESULTADOS PROYECTO
$app->match('/projectResults/{id}', function(Request $request, $id) use ($app){
    $em = $app['orm.em'];

    $projectsRepository = $em->getRepository('\Dev\Pub\Entity\Project');
    $project = $projectsRepository->findOneBy(array('id' => $id));

    $keywords = $project->getKeywords();

    foreach ($keywords as $keyword) {
        $url = $app['url_generator']->generate('projectResultsKeyword', array('projectId' => $id, 'keywordId' => $keyword->getId()));
        return $app->redirect($url);
    }
})->bind('projectResults');

//CHEQUEAR SCRAPPEOS PENDIENTES
$app->match('/checkPendingWork', function(Request $request) use ($app){
    $em = $app['orm.em'];

    $q = $em->createQuery("select p from \Dev\Pub\Entity\Project p where p.start_date < CURRENT_TIMESTAMP() and p.end_date > CURRENT_TIMESTAMP()");
    $currentProjects = $q->getResult();

    if($currentProjects)
    {
        //recorrer proyectos para ir haciendo las peticiones
        //$projectsArray = $currentProjects->toArray();
        $result ="";
        for($i=0;$i<count($currentProjects);$i++)
        {
            $result = $result."<br>".$currentProjects[$i]->getName();
            echo "<p>========>ACTUALIZAMOS EL PROYECTO: ".$currentProjects[$i]->getName()."</p>";
            $keywords = $currentProjects[$i]->getKeywords();
            for($j=0;$j<count($keywords);$j++){
                $result = $result."<br>-keyword: ".$keywords[$j]->getName();
                echo "<p>===>BUSCAMOS RESULTADOS PARA KEYWORD: ".$keywords[$j]->getName()."</p>";
                $currentKeyword = str_replace(' ', '+', $keywords[$j]->getName());
                //$url = "http://www.".$currentProjects[$i]->getSearchEngine()."/search?q=".$currentKeyword."&hl=".$currentProjects[$i]->getLanguage()."&gl=".$currentProjects[$i]->getCountry()."&pws=0";
                $url = "http://www.google.es/search?q=".$currentKeyword."&hl=es&gl=es&pws=0";


                $tries = 5;

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,10); 
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                //$proxies = array('108.186.244.74:80', '108.186.244.222:80', '108.186.244.73:80', '192.126.190.49:80', '192.126.190.96:80');
                $proxies = $app['proxies'];


                if(isset($proxies)){
                    $tries = sizeof($proxies);
                }else{
                    $tries = 1;
                }
                

                $sucess = FALSE;

                while($tries > 0 && $sucess==FALSE){
                    echo "<p>Quedan ".$tries." intentos</p>";
                    if(isset($proxies)){
                        $proxy = $proxies[array_rand($proxies)];
                        curl_setopt($ch, CURLOPT_PROXY, $proxy);
                        echo "<p>Usamos el proxy: ".$proxy."</p>";
                    }
                    $returnHtml = curl_exec($ch);
                    switch ($returnHtml) {
                        case "FALSE":
                            echo "<p>curl_exec ha devuelto: ".$returnHtml."</p>";
                            echo "<p>No se ha podido completar la petición</p>";
                            $tries--;
                            break;
                        
                        default:
                            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            if($httpcode == 302){
                                //IP baneada, google nos lleva al captcha
                                echo "<p>Google nos envía captcha, esta IP está baneada temporalmente</p>";
                                echo "<p>Eliminamos el proxy ".$proxy." de la lista para esta iteración</p>";
                                $proxies = array_diff($proxies, array($proxy));
                                $tries--;
                            }
                            else if($httpcode == 200){
                                //Hemos podido obtener el html!
                                echo "<p>HTML obtenido con éxito!</p>";
                                $sucess = TRUE;
                            }else{
                                echo "<p>La petición se ha realizado correctamente pero se nos ha devuelvo un ".$httpcode."</p>";

                            }
                            break;
                    }

                }

                if($sucess==FALSE){
                    return "No se ha podido scrappear la URL";
                }


                $tempHtml = new DOMDocument;
                @$tempHtml->loadHtml($returnHtml);
                /*if($httpcode != 200){
                    return "Google no ha devuelto un 200, probablemente nos haya baneado";
                }else if($tempHtml == FALSE){
                    return "Ha habido algún error al intentar scrappear a Google";
                }*/

                $serp = new \Dev\Pub\Entity\SERP();
                $serp->setKeyword($keywords[$j]);
                
                $serp->setTimestamp(new \DateTime());
                $saveHtmlPath = "../htmlScrapped/".$currentProjects[$i]->getId()."/".$keywords[$j]->getId();
                $filename = $serp->getTimeStamp()->getTimestamp().".html";
                $pathAndFilename = $saveHtmlPath."/".$filename;
                if(!is_dir($saveHtmlPath)){
                    if(!mkdir($saveHtmlPath, 0777, true)){
                        die("Fallo al crear las carpetas");
                    }
                }
                $tempHtml->saveHTMLFile($pathAndFilename);
                $serp->setHtml($pathAndFilename);

                $xpath = new DOMXPath($tempHtml);

                //esto parte el html en los serpresult unicamente, con lo cual podemos ir uno por uno comprobando el tipo 
                //y guardando la info que corresponda
                $nodelist = $xpath->query("//li[@class='g']");

                
                $posicionNews = 1;
                $posicionImages = 1;
                $posicionOrganic = 1;
                $organicCount = 0;

                //hacemos un bucle para recorrer los bloques de resultados
                foreach ($nodelist as $serpnode) 
                {
                    $tempSERPresult = new \Dev\Pub\Entity\SERPResult();
                    $tempSERPresult->setType("0");
                    $indexNews = 0;
                    

                    //es news?
                    $links = $xpath->query(".//a/@href", $serpnode);
                    foreach ($links as $link) 
                    {
                        if (strpos($link->nodeValue, 'QqQIw') != false) 
                        {
                            //es news, un link
                            $tempSERPresult = new \Dev\Pub\Entity\SERPResult();
                            $tempSERPresult->setType("news");
                            $children = $serpnode->childNodes; 
                            $htmlNews ="";
                            foreach ($children as $child) { 
                                $tmp_doc = new DOMDocument(); 
                                $tmp_doc->appendChild($tmp_doc->importNode($child,true));        
                                $htmlNews .= $tmp_doc->saveHTML(); 
                            } 
                            $htmlNews = str_replace("\n", '', $htmlNews); // remove new lines
                            $htmlNews = str_replace("\r", '', $htmlNews); // remove carriage returns
                            if($serp->getNewsHtml() == ""){
                                $serp->setNewsHtml(serialize($htmlNews));
                            }

                            //pedimos el title
                            $title = $xpath->query(".//a[contains(@href, 'QqQIw')]", $serpnode);
                            $tempSERPresult->setTitle($title->item($indexNews)->nodeValue);

                            //el site
                            $newssite = $xpath->query(".//a[contains(@href, 'QqQIw')]//..//div//cite", $serpnode);
                            $tempSERPresult->setSite($newssite->item($indexNews)->nodeValue);

                            //la url limpia
                            preg_match('~q=(https?://.*)&sa~', $link->nodeValue, $url);
                            $tempSERPresult->setUrl($url[1]);
                            

                            //el ranking
                            $tempSERPresult->setRank($posicionOrganic);
                            $tempSERPresult->setSubrank($posicionNews);

                            //el hace x horas/minutos
                            $time = $xpath->query(".//a[contains(@href, 'QqQIw')]//..//div//span//span[@class='nobr']");
                            $timeindex = 2*($indexNews);
                            $tempSERPresult->setUpdatedTime($time->item($timeindex)->nodeValue);

                            //la descripción
                            $description = $xpath->query(".//a[contains(@href, 'QqQIw')]//..//div//span[@class='st']");
                            if($indexNews==0){
                                $tempSERPresult->setDescription($description->item($indexNews)->nodeValue);  
                            }

                            $posicionNews++;
                            $indexNews++;

                            /*
                            *   Código para scrappear de los propios resultados
                            */
                            $ResultHtml = new DOMDocument;
                            @$ResultHtml->loadHtmlFile($tempSERPresult->getUrl());
                            $tempXpath = new DOMXPath($ResultHtml);

                            //title
                            $title = $tempXpath->query("//title");
                            if($title->length != 0){
                                $tempSERPresult->setUrlTitle($title->item(0)->nodeValue);
                            }

                            //h1
                            $h1 = $tempXpath->query("//h1[contains(.,' ')]");
                            if($h1->length != 0){
                                $tempSERPresult->setUrlH1($h1->item(0)->nodeValue);
                            }

                            //h2
                            $h2 = $tempXpath->query("//h2");
                            if($h2->length != 0){
                                $tempSERPresult->setUrlH2($h2->item(0)->nodeValue);
                            }

                            //date
                            $urlDate = $tempXpath->query("//meta[@name='date']/@content");
                            if($urlDate->length != 0){
                                $tempSERPresult->setUrlDate($urlDate->item(0)->nodeValue);
                            }

                            $urlDateIssued = $tempXpath->query("//meta[@name='DC.date.issued']/@content");
                            if($urlDateIssued->length != 0){
                                $tempSERPresult->setUrlDateIssued($urlDateIssued->item(0)->nodeValue);
                            }

                            $urlCharacterCount = $tempXpath->query("//p");
                            $tempCount = 0;
                            foreach ($urlCharacterCount as $p) {
                                $tempCount += strlen($p->nodeValue);
                            }
                            if($tempCount != 0){
                                $tempSERPresult->setUrlCharacterCount($tempCount);
                            }

                            $URLLinks = $tempXpath->query("//p//a/@href");
                            
                            $tempDomain = "";
                            $inLinksCount = 0;
                            $outLinksCount = 0;
                            
                            preg_match('"^(?:https?:\/\/)?(?:www\.)?([^\/]+)"', $tempSERPresult->getUrl(), $tempDomain);

                            foreach ($URLLinks as $linkHref) {
                                //descartamos anclas, js, enlaces internos relativos (no se suelen dar en contenido)      
                                if(strpos($linkHref->nodeValue, "http") === 0){
                                    //si es FALSO, es un link externo
                                    if(strpos($linkHref->nodeValue, $tempDomain[1]) === FALSE){
                                        $outLinksCount++;
                                    }else{
                                        $inLinksCount++;
                                    }
                                }
                            }

                            $tempSERPresult->setUrlOutLinksCount($outLinksCount);
                            $tempSERPresult->setUrlInLinksCount($inLinksCount);

                            $apiEndPoint = "https://free.sharedcount.com";
                            $apiKey = "4a70e6281740d91abe3fab751110db54cad34637";
                            $apiCallUrl = $apiEndPoint."?url=".$tempSERPresult->getUrl()."&apikey=".$apiKey;
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL,$apiCallUrl);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                            $returnJson = curl_exec($ch);
                            curl_close ($ch);
                            $counts = json_decode($returnJson, true);
                            $tempSERPresult->setUrlTweetCount($counts['Twitter']);
                            $tempSERPresult->setUrlFbLikeCount($counts['Facebook']['like_count']);
                            $tempSERPresult->setUrlFbShareCount($counts['Facebook']['share_count']);
                            $tempSERPresult->setUrlFbCommentCount($counts['Facebook']['commentsbox_count']);
                            $tempSERPresult->setUrlFbTotalCount($counts['Facebook']['total_count']);
                            $tempSERPresult->setUrlPlusOneCount($counts['GooglePlusOne']);

                            /*$curl = curl_init();
                            $apiKey = "AIzaSyDtMneF9wgZDs5dUE6QbxQCN6-dkiraBUs";
                            $url = $tempSERPresult->getUrl();
                            curl_setopt_array($curl, array(
                                CURLOPT_RETURNTRANSFER => 1,
                                CURLOPT_URL => 'https://www.googleapis.com/pagespeedonline/v3beta1/mobileReady?key='.$apiKey.'&url='.$url.'&strategy=mobile',
                            ));
                            $resp = curl_exec($curl);
                            curl_close($curl);
                            

                            $resultPageSpeed = json_decode($resp, true);

                            if($resultPageSpeed['ruleGroups']['USABILITY']['pass'] == TRUE){
                                $tempSERPresult->setUrlMobileFriendly(1);
                            }else{
                                $tempSERPresult->setUrlMobileFriendly(0);
                            }*/


                            $serp->addSerpResult($tempSERPresult);
                        }else if(strpos($link->nodeValue, 'QpwJ') != false){
                            //es news, la imagen
                            $tempSERPresult = new \Dev\Pub\Entity\SERPResult();
                            $tempSERPresult->setType("news-image");
                            $newssite = $xpath->query(".//a[contains(@href, 'QpwJ')]//..//span", $serpnode);
                            $tempSERPresult->setSite($newssite->item(0)->nodeValue);
                            preg_match('~q=(https?://.*)&sa~', $link->nodeValue, $url);
                            $tempSERPresult->setUrl($url[1]);
                            $tempSERPresult->setRank($posicionOrganic);
                            $tempSERPresult->setSubrank(0);
                            $serp->addSerpResult($tempSERPresult);
                        }else if(strpos($link->nodeValue, 'QFjA') != false){
                            //es un resultado orgánico normal
                            $tempSERPresult = new \Dev\Pub\Entity\SERPResult();
                            $tempSERPresult->setType("normal");
                            $title = $xpath->query(".//a[contains(@href, 'QFjA')]", $serpnode);
                            $tempSERPresult->setTitle($title->item(0)->nodeValue);
                            preg_match('~q=(https?://.*)&sa~', $link->nodeValue, $url);
                            $tempSERPresult->setUrl($url[1]);
                            if($posicionNews!=1 || $posicionImages!=1){
                                $posicionNews=1;
                                $posicionImages=1;
                            }
                            $tempSERPresult->setRank($posicionOrganic);
                            $description = $xpath->query(".//a[contains(@href, 'QFjA')]//..//..//span[@class='st']");

                            $tempSERPresult->setDescription($description->item($organicCount)->nodeValue);
                            $organicCount++;
                            $serp->addSerpResult($tempSERPresult);
                        }else if(strpos($link->nodeValue, 'QwW4w') != false){
                            $tempSERPresult = new \Dev\Pub\Entity\SERPResult();
                            $tempSERPresult->setType("images");
                            preg_match('~q=(https?://.*)&sa~', $link->nodeValue, $url);
                            $tempSERPresult->setUrl($url[1]);
                            $tempSERPresult->setRank($posicionOrganic);
                            $tempSERPresult->setSubrank($posicionImages);
                            $posicionImages++;
                            $serp->addSerpResult($tempSERPresult);
                        }
                    }
                    $posicionOrganic++;   
                }
                $em->persist($serp); 
            }
        }
        $em->flush();
        return TRUE;
    }else{
        return "No hay proyectos en marcha";
    }

})->bind('checkPendingWork');
//LOGOUT
$app->match('/logout', function () use ($app) {
    $app['session']->clear();

    return $app->redirect($app['url_generator']->generate('homepage'));
})->bind('logout');


//CACHEPAGE
$app->get('/page-with-cache', function () use ($app) {
    $response = new Response($app['twig']->render('page-with-cache.html.twig', array('date' => date('Y-M-d h:i:s'))));
    $response->setTtl(10);

    return $response;
})->bind('page_with_cache');


//ERRORES
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            var_dump('exception handler', get_class($e));
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return new Response($message, $code);
});

return $app;
