<?php
    
    use Adianti\Database\TRecord;
    use Adianti\Database\TTransaction;
    
    require_once 'init.php';
    
    $objeto = new Converter();
    exit;
    
    class Converter {
        public function converter()
        {
            try {
                echo "SystemGroup <br>";
                $objects = self::obterDados(SystemGroup::class);
                self::gravarDados($objects, SystemGroup::class);
    
                echo "SystemProgram <br>";
                $objects = self::obterDados(SystemProgram::class);
                self::gravarDados($objects, SystemProgram::class);
    
                echo "SystemGroupProgram <br>";
                $objects = self::obterDados(SystemGroupProgram::class);
                self::gravarDados($objects, SystemGroupProgram::class);
    
                echo "SystemPreference <br>";
                $objects = self::obterDados(SystemPreference::class);
                self::gravarDados($objects, SystemPreference::class);

                echo "SystemUnit <br>";
                $objects = self::obterDados(SystemUnit::class);
                self::gravarDados($objects, SystemUnit::class);
    
                echo "SystemUser <br>";
                $objects = self::obterDados(SystemUser::class);
                self::gravarDados($objects, SystemUser::class);
    
                echo "SystemUserGroup <br>";
                $objects = self::obterDados(SystemUserGroup::class);
                self::gravarDados($objects, SystemUserGroup::class);
    
                echo "SystemUserProgram <br>";
                $objects = self::obterDados(SystemUserProgram::class);
                self::gravarDados($objects, SystemUserProgram::class);
    
                echo "SystemUserUnit <br>";
                $objects = self::obterDados(SystemUserUnit::class);
                self::gravarDados($objects, SystemUserUnit::class);
    
                echo "SystemDocument <br>";
                $objects = self::obterDocument(SystemDocument::class);
                self::gravarDados($objects, SystemDocument::class);
    
                echo "SystemDocumentCategory <br>";
                $objects = self::obterDocument(SystemDocumentCategory::class);
                self::gravarDados($objects, SystemDocumentCategory::class);
    
                echo "SystemDocumentGroup <br>";
                $objects = self::obterDocument(SystemDocumentGroup::class);
                self::gravarDados($objects, SystemDocumentGroup::class);
    
                echo "SystemDocumentUser <br>";
                $objects = self::obterDocument(SystemDocumentUser::class);
                self::gravarDados($objects, SystemDocumentUser::class);
    
                echo "SystemMessage <br>";
                $objects = self::obterDocument(SystemMessage::class);
                self::gravarDados($objects, SystemMessage::class);
    
                echo "SystemNotification <br>";
                $objects = self::obterDocument(SystemNotification::class);
                self::gravarDados($objects, SystemNotification::class);
    
            } catch ( Exception $e ) {
                echo "Erro -> ".$e->getMessage();
            }
        }
    
        public function obterDados( $tabela )
        {
            TTransaction::open( 'permission' );
            $objects = $tabela::all();
            TTransaction::close();
            return $objects;
        }
    
        public function obterDocument( $tabela )
        {
            TTransaction::open( 'communication' );
            $objects = $tabela::all();
            TTransaction::close();
            return $objects;
        }
    
    
        public function gravarDados ($objects, $tabela )
        {
            TTransaction::open( 'afincco' );
            foreach ( $objects as $object ) {
                $novo = $tabela::find( $object->id );
                if ( !$novo ) {
                    $object->store();
                }
            }
            TTransaction::close();
        }
    
    }