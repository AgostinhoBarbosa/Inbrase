<?php
/**
 * KeepNavigation - Classe que gerencia os dados em sessão para manter a paginação
 *
 * @version 1.00.00
 * @author Allan Kehl <allan@plenatech.com.br>
 *
 * 04/07/2015 - Allan Kehl
 *    Classe criada com base no código desenvolvido pelo Marco
 * 04/07/2018 - Marco Driemeyer
 *    Classe verificada
 */
class KeepNavigation {

    /**
     * Metodo usado para limpar os dados da paginação quando se realiza um filtro ou
     * ainda quando se limpa o filtro de uma pagina
     * @param $class_name nome da classe para a qual deseja-se manter a paginação
     */
    public static function clear($class_name)
    {
        // dados do keepNavigation
        TSession::setValue("{$class_name}_filter_order", NULL);
        TSession::setValue("{$class_name}_filter_offset", NULL);
        TSession::setValue("{$class_name}_filter_limit", NULL);
        TSession::setValue("{$class_name}_filter_direction", NULL);
        TSession::setValue("{$class_name}_filter_page", NULL);
        TSession::setValue("{$class_name}_filter_first_page", NULL);
    }

    /**
     * Metodo usado para manter a paginação mesmo quando se entra ou sai da listagem
     * esse metodo é chamado pelo onReload com os parametros recebidos, então ele os
     * armazena na sessão para devolver os mesmos quando a pagina for recarregada
     * os dados aqui colocados na sessão somente são limpos quando a pagina é limpa
     * @param $param array com os parâmetros encaminhados para o onRelaod
     * @param $class_name nome da classe para a qual deseja-se manter a paginação
     * @return array com os parametros de paginação, salvos quando entrada é null ou igual 
     *         ao de entrada quando se esta salvando a paginação
     */
    public static function update($param, $class_name)
    {
        if (!isset($param['order'])){
            if (TSession::getValue("{$class_name}_filter_order"))
                $param['order'] = TSession::getValue("{$class_name}_filter_order");
        } else {
            TSession::setValue("{$class_name}_filter_order", $param['order']);
        }
        
        if (!isset($param['offset'])){
            if (TSession::getValue("{$class_name}_filter_offset"))
                $param['offset'] = TSession::getValue("{$class_name}_filter_offset");
        } else {
            TSession::setValue("{$class_name}_filter_offset", $param['offset']);
        }
        
        if (!isset($param['limit'])){
            if (TSession::getValue("{$class_name}_filter_limit"))
                $param['limit'] = TSession::getValue("{$class_name}_filter_limit");
        } else {
            TSession::setValue("{$class_name}_filter_limit", $param['limit']);
        }
        
        if (!isset($param['direction'])){
            if (TSession::getValue("{$class_name}_filter_direction"))
                $param['direction'] = TSession::getValue("{$class_name}_filter_direction");
        } else {
            TSession::setValue("{$class_name}_filter_direction", $param['direction']);
        }
        
        if (!isset($param['page'])){
            if (TSession::getValue("{$class_name}_filter_page"))
                $param['page'] = TSession::getValue("{$class_name}_filter_page");
        } else {
            TSession::setValue("{$class_name}_filter_page", $param['page']);
        }
        
        if (!isset($param['first_page'])){
            if (TSession::getValue("{$class_name}_filter_first_page"))
                $param['first_page'] = TSession::getValue("{$class_name}_filter_first_page");
        } else {
            TSession::setValue("{$class_name}_filter_first_page", $param['first_page']);
        }
        
        // retorna os parametros eventualmente modificados
        return $param;
    }
}