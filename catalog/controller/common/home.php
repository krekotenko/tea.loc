<?php

class ControllerCommonHome extends Controller
{
    public function productMapper($products)
    {
        $mappedProducts = [];
        foreach ($products as $product) {
            if ($product['image']) {
                $thumb = $this->model_tool_image->resize($product['image'], 250, 250);
            } else {
                $thumb = '';
            }
            $mappedProducts[] = [
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                'thumb' => $thumb,
                'name' => $product['name'],
                'product_id' => $product['product_id'],
                'price' => $product['price'],
                'description' => html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')
            ];
        }
        return $mappedProducts;
    }

    public function index()
    {
        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        if (isset($this->request->get['route'])) {
            $this->document->addLink($this->config->get('config_url'), 'canonical');
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        // my content
        $this->load->language('product/category');

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $data['categories'] = array();

        foreach ($this->model_catalog_category->getCategories(array()) as $category) {
            $filter = array(
                'filter_category_id' => $category['category_id']
            );
            $products = $this->model_catalog_product->getProducts($filter);
            $subcategories = array();
            foreach ($this->model_catalog_category->getCategories($category['category_id']) as $subcategory) {
                $filter2 = array(
                    'filter_category_id' => $subcategory['category_id']
                );
                $subcat_products = $this->model_catalog_product->getProducts($filter2);
                if (count($subcat_products)) {
                    $subcategories[] = [
                        'category_id' => $subcategory['category_id'],
                        'name' => $subcategory['name'],
                        'products' => $this->productMapper($subcat_products),
                    ];
                }
            };


            $data['categories'][] = [
                'category_id' => $category['category_id'],
                'name' => $category['name'],
                'description' => html_entity_decode($category['description'], ENT_QUOTES, 'UTF-8'),
                'products' => $this->productMapper($products),
                'categories' => $subcategories,
            ];

        }


        $data['chosen_tab'] = !empty($this->request->get['tab']) ? $this->request->get['tab'] : 1;

        if(isset($GLOBALS["first_launch"])) {
            $this->response->setOutput($this->load->view('common/first_launch', $data));
        } else {
            $this->response->setOutput($this->load->view('common/home', $data));
        }


//        var_dump($data['categories']);
    }
}
