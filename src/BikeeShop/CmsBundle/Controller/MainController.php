<?php

namespace BikeeShop\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BikeeShop\CmsBundle\Entity\Customer;
use BikeeShop\CmsBundle\Form\CustomerType;


class MainController extends Controller
{
    /**
     * @Route("/accueil", name="accueil_cms")
     */
    public function indexAction()
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://localhost/projetXML/projet_xml_webservice_serveur/products/page-1',
            CURLOPT_USERAGENT => 'All products page 1'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        //var_dump(json_decode($resp));
        $products = json_decode($resp);
        //var_dump($products);
        // Close request to clear up some resources
        curl_close($curl);

        //var_dump($products);

        return $this->render('BikeeShopCmsBundle:Main:index.html.twig', array(
            'products' => $products->Products
        ));
    }

    /**
     * @Route("/men/{pageNumber}", defaults={"pageNumber" = 1}, name="hommes")
     */
    public function hommesAction($pageNumber)
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://localhost/projetXML/projet_xml_webservice_serveur/category/1/page-' . $pageNumber,
            CURLOPT_USERAGENT => 'products for men'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        //var_dump(json_decode($resp));
        $products = json_decode($resp);

        // Close request to clear up some resources
        curl_close($curl);

        return $this->render('BikeeShopCmsBundle:Main:hommes.html.twig', array(
            'products' => $products->Products,
            'maxPage' => $products->maxPage,
            'pageNumber' => $pageNumber
        ));
    }

    /**
     * @Route("/woman/{pageNumber}", defaults={"pageNumber" = 1}, name="femmes")
     */
    public function femmesAction($pageNumber)
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://localhost/projetXML/projet_xml_webservice_serveur/category/2/page-' . $pageNumber,
            CURLOPT_USERAGENT => 'products for woman'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        //var_dump(json_decode($resp));
        $products = json_decode($resp);
        // Close request to clear up some resources
        curl_close($curl);

        return $this->render('BikeeShopCmsBundle:Main:femmes.html.twig', array(
            'products' => $products->Products,
            'maxPage' => $products->maxPage,
            'pageNumber' => $pageNumber
        ));
    }

    /**
     * @Route("/shop", name="shop_cms")
     */
    public function shopAction()
    {
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here

        for($i = 1; $i<=4; $i++) {
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://localhost/projetXML/projet_xml_webservice_serveur/product/' . $i,
                CURLOPT_USERAGENT => 'product by id'
            ));
            // Send the request & save response to $resp
            $resp = curl_exec($curl);
            $productMen[] = json_decode($resp);
        }

        for($i = 5; $i<=8; $i++) {
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://localhost/projetXML/projet_xml_webservice_serveur/product/' . $i,
                CURLOPT_USERAGENT => 'product by id'
            ));
            // Send the request & save response to $resp
            $resp = curl_exec($curl);
            $productWoman[] = json_decode($resp);
        }

        curl_close($curl);

        return $this->render('BikeeShopCmsBundle:Main:shop.html.twig', array(
            'productsMen' => $productMen,
            'productsWoman' => $productWoman
        ));
    }

    /**
     * @Route("/product/{slug}/{id}", requirements={"id" = "\d+"}, name="detail_product")
     */
    public function detailAction($id, $slug)
    {
        $url = $this->generateUrl('detail_product', array('id' => $id, 'slug' => $slug));
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://localhost/projetXML/projet_xml_webservice_serveur/product/' . $id,
            CURLOPT_USERAGENT => 'product by id'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        //var_dump(json_decode($resp));
        $product = json_decode($resp);
        // Close request to clear up some resources
        curl_close($curl);

        return $this->render('BikeeShopCmsBundle:Main:bike' . $id . '.html.twig', array(
            'url' => $url,
            'product' => $product
        ));
    }

    /**
     * @Route("/add/product/cart/{idProduct}", defaults={"idProduct" = 0}, name="addProductBasket")
     */
    public function addProductBasketAction($idProduct)
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://localhost/projetXML/projet_xml_webservice_serveur/product/' . $idProduct,
            CURLOPT_USERAGENT => 'All products page 1'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        $productToAdd = json_decode($resp);
        $exist = FALSE;

        if (empty($_SESSION['Products'])) {
            array_push($_SESSION['Products'], $productToAdd);
        } else {
            foreach ($_SESSION['Products'] as $product) {
                // Voir si le produit est deja dans le panier
                if (($productToAdd->idProduct == $product->idProduct) && ($exist == FALSE)) {
                    $exist = TRUE;
                }
            }
            //Ajouter au panier le vélo que s'il n'y est pas déjà
            if ($exist == FALSE) {
                array_push($_SESSION['Products'], $productToAdd);
            }
        }

        //var_dump($_SESSION['Products']);

        // Close request to clear up some resources
        curl_close($curl);

        return $this->render('BikeeShopCmsBundle:Main:cart.html.twig', array(
            'listProducts' => $_SESSION['Products']
        ));
    }

    /**
     * @Route("/informations", name="customerInfos")
     */
    public function customerInfosAction(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(new CustomerType(), $customer);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                //Actions à effecter après validation du form
                //$this->get('session')->getFlashBag()->add('notice', "Merci, votre message
                //a bien été pris en compte ! ");

                // POST METHOD to create customer
                $post = [
                    'firstname' => $customer->getFirstName(),
                    'lastname' => $customer->getLastName(),
                    'mail' => $customer->getEmail()
                ];

                $ch = curl_init('http://localhost/projetXML/projet_xml_webservice_serveur/createUser');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

                // execute!
                $response = curl_exec($ch);

                // close the connection, release resources used
                curl_close($ch);

                // do anything you want with your response
                print_r($response);

                //Redirection afin d'éviter de 're-posting'
                //return $this->redirect($this->generateUrl('customerInfos'));
            }
        }

        return $this->render('BikeeShopCmsBundle:Main:customerInfos.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cartAction()
    {

        return $this->render('BikeeShopCmsBundle:Main:cart.html.twig', array(
            'listProducts' => ''
        ));
    }

    /**
     * @Route("/1", name="bike1")
     */
    public
    function bike1Action()
    {

        return $this->render('BikeeShopCmsBundle:Main:bike1.html.twig');
    }

    /**
     * @Route("/3", name="bike3")
     */
    public
    function bike3Action()
    {

        return $this->render('BikeeShopCmsBundle:Main:bike2.html.twig');
    }

    /**
     * @Route("/4", name="bike4")
     */
    public
    function bike4Action()
    {

        return $this->render('BikeeShopCmsBundle:Main:bike3.html.twig');
    }

    /**
     * @Route("/6", name="bike6")
     */
    public
    function bike6Action()
    {

        return $this->render('BikeeShopCmsBundle:Main:bike4.html.twig');
    }

    /**
     * @Route("/s1", name="s1")
     */
    public
    function s1Action()
    {

        return $this->render('BikeeShopCmsBundle:Main:bike5.html.twig');
    }

    /**
     * @Route("/s2", name="s2")
     */
    public
    function s2Action()
    {

        return $this->render('BikeeShopCmsBundle:Main:bike6.html.twig');
    }

    /**
     * @Route("/s3", name="s3")
     */
    public
    function s3Action()
    {

        return $this->render('BikeeShopCmsBundle:Main:bike7.html.twig');
    }

    /**
     * @Route("/s4", name="s4")
     */
    public
    function s4Action()
    {

        return $this->render('BikeeShopCmsBundle:Main:bike8.html.twig');
    }

}
