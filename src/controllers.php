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
    $app['session']->getFlashBag()->add('warning', 'Warning flash message');
    $app['session']->getFlashBag()->add('info', 'Info flash message');
    $app['session']->getFlashBag()->add('success', 'Success flash message');
    $app['session']->getFlashBag()->add('error', 'Error flash message');

    return $app['twig']->render('index.html.twig');
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
    $entity = new \Dev\Pub\Entity\Project();

    $form = $app['form.factory']->create(new \Dev\Pub\Project\ProjectType(), $entity); 
    $form->handleRequest($request);
    if ($form->isValid()) {
        $em->persist($entity);
        $em->flush();
    } 


    return $app['twig']->render('newproject.html.twig', array('form' => $form->createView()));
})->bind('newProject');

//AÑADIR KEYWORDS
$app->match('/addKeywords', function(Request $request) use ($app){
    $em = $app['orm.em'];
    $entity = new \Dev\Pub\Entity\Keyword();

    $form = $app['form.factory']->create(new \Dev\Pub\Keyword\KeywordType(), $entity); 
    $form->handleRequest($request);
    if ($form->isValid()) {
        $em->persist($entity);
        $em->flush();
    } 


    return $app['twig']->render('addkeywords.html.twig', array('form' => $form->createView()));
})->bind('addKeywords');

//VER RESULTADOS PROYECTO
$app->match('/projectResults/{id}', function(Request $request, $id) use ($app){
    $em = $app['orm.em'];

    $pojectsRepository = $em->getRepository('\Dev\Pub\Entity\Project');
    $project = $pojectsRepository->findOneBy(array('id' => $id));

    $keywords = $project->getKeywords();

    foreach ($keywords as $keyword) {
        //para cada keyword pintaremos una gráfica, con los top dominios y la evolución de cada uno.
        //de momento vamos a intentar sacar la gráfica solo para el mundo para probar el formato y que todo va bien
        $serps = $keyword->getSerps();
        $i=0;
        $serpNumber=0;


        $visibilityArray = array();
        $rankingArray = array();
        $scrappedTimes = array();
        
        if($keyword->getName()=="lina morgan"){
            foreach ($serps as $serp) {                
                $serpresults = $serp->getSerpResults();
                $scrappedTimes[] = date_format($serp->getTimestamp(), 'Y-m-d H:i:s');
                foreach ($serpresults as $serpresult) {
                    if($serpresult->getType()=="news"){
                        $currentSite = $serpresult->getSite();

                        //Tenemos que saber si es la primera vez que aparece el site o no. 
                        if(array_key_exists($currentSite, $rankingArray)){
                            //ya tenemos datos del site! tenemos que saber si hay vacíos o no
                            if(sizeof($rankingArray[$currentSite])<$serpNumber){
                                //hay que rellenar
                                for($temp=sizeof($rankingArray[$currentSite]); $temp<($serpNumber); $temp++){
                                    //por cada hueco añadimos un null
                                    $rankingArray[$currentSite][] = array($scrappedTimes[$temp] => null);
                                }
                                //al terminar añadimos el valor actual
                                $rankingArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $serpresult->getSubrank());
                            }
                            else if(sizeof($rankingArray[$currentSite]) == $serpNumber){
                                //es el valor que toca
                                $rankingArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $serpresult->getSubrank());
                            }

                            //si sizeof($rankingArray[$currentSite]) es mayor que $i, es que hay más de un resultado del mismo site, 
                            //por lo que no añadimos posicion (mantenemos la que tuviese)
                            //calculamos la visibilidad para cualquiera de los casos
                            //ya existe valor de visibilidad porque siempre que hay una posicion hay un valor 
                            //de visibilidad
                            $visibilityArray[$currentSite] = $visibilityArray[$currentSite] + (4-$serpresult->getSubRank());
                        }else{
                            //es la primera vez que aparece el site así que simplemente asignamos los valores
                            for($temp=0;$temp<$serpNumber;$temp++){
                                $rankingArray[$currentSite][] = array($scrappedTimes[$temp] => null);
                            }
                            $rankingArray[$currentSite][] = array($scrappedTimes[$serpNumber] => $serpresult->getSubrank());
                            $visibilityArray[$currentSite] = 4-$serpresult->getSubrank();
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
                $serpNumber++;
            }
        }

        $top1 = "";
        $top2 = "";
        $top3 = "";
        $topVisibility1 = 0;
        $topVisibility2 = 0;
        $topVisibility3 = 0;
        foreach ($visibilityArray as $key => $value) {
            if($value > $topVisibility1){
                $topVisibility1 = $value;
                $top1 = $key;
            }else if($value > $topVisibility2){
                $topVisibility2 = $value;
                $top2 = $key;
            }else if($value > $topVisibility3){
                $topVisibility3 = $value;
                $top3 = $key;
            }
        }


       if($top1 != ""){       
            for($x=0;$x<sizeof($rankingArray[$top1]);$x++){
                $currentTime = $scrappedTimes[$x];
                $data[] = array($scrappedTimes[$x], $rankingArray[$top1][$x][$currentTime], $rankingArray[$top2][$x][$currentTime], $rankingArray[$top3][$x][$currentTime]);
            }
           
           $encoders = array(new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());

            $serializer = new Serializer($normalizers, $encoders);
            $data = $serializer->serialize($data, 'json');
            $columns = array('Minuto', $top1, $top2, $top3);
            return $app['twig']->render('projectresults.html.twig', array('data' => $data, 'columns'=>$columns, 'keyword'=>$keyword->getName()));
       }

    }

    $encoders = array(new JsonEncoder());
    $normalizers = array(new GetSetMethodNormalizer());

    $serializer = new Serializer($normalizers, $encoders);
    $data = $serializer->serialize($data, 'json');
    $currentKeyword = $keyword->getName();



    return $app['twig']->render('projectresults.html.twig', array('data' => $data));
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
            $keywords = $currentProjects[$i]->getKeywords();
            for($j=0;$j<count($keywords);$j++){
                $result = $result."<br>-keyword: ".$keywords[$j]->getName();
                $currentKeyword = str_replace(' ', '+', $keywords[$j]->getName());
                //$url = "http://www.".$currentProjects[$i]->getSearchEngine()."/search?q=".$currentKeyword."&hl=".$currentProjects[$i]->getLanguage()."&gl=".$currentProjects[$i]->getCountry()."&pws=0";
                $url = "http://www.google.es/search?q=".$currentKeyword."&hl=".$currentProjects[$i]->getLanguage()."&gl=".$currentProjects[$i]->getCountry()."&pws=0";
                $tempHtml = new DOMDocument;
                @$tempHtml->loadHtmlFile($url);
                if($tempHtml == FALSE){
                    echo "<br>ERROR<br>";
                }

                $serp = new \Dev\Pub\Entity\SERP();
                $serp->setKeyword($keywords[$j]);
                
                //$date = new DateTime();
                //$timestamp = $date->getTimestamp();
                $serp->setTimestamp(new \DateTime());
                $serp->setHtml($tempHtml->saveXML());

                //$xpath = new DOMXPath($serp->getHtml());
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
        return $result;
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
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return new Response($message, $code);
});

return $app;
