<?php
    
    use Phinx\Db\Adapter\MysqlAdapter;
    
    class InbraseInicial extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER DATABASE CHARACTER SET 'utf8mb4';");
        $this->execute("ALTER DATABASE COLLATE='utf8mb4_general_ci';");
        $this->table('system_request_log', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('endpoint', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->addColumn('logdate', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'endpoint',
            ])
            ->addColumn('log_year', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 4,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'logdate',
            ])
            ->addColumn('log_month', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_year',
            ])
            ->addColumn('log_day', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_month',
            ])
            ->addColumn('session_id', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_day',
            ])
            ->addColumn('login', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'session_id',
            ])
            ->addColumn('access_ip', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'login',
            ])
            ->addColumn('class_name', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'access_ip',
            ])
            ->addColumn('http_host', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'class_name',
            ])
            ->addColumn('server_port', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'http_host',
            ])
            ->addColumn('request_uri', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'server_port',
            ])
            ->addColumn('request_method', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'request_uri',
            ])
            ->addColumn('query_string', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'request_method',
            ])
            ->addColumn('request_headers', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'query_string',
            ])
            ->addColumn('request_body', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'request_headers',
            ])
            ->addColumn('request_duration', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'request_body',
            ])
            ->create();
        $this->table('system_message', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('system_user_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('system_user_to_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'system_user_id',
            ])
            ->addColumn('subject', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'system_user_to_id',
            ])
            ->addColumn('message', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'subject',
            ])
            ->addColumn('dt_message', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'message',
            ])
            ->addColumn('checked', 'char', [
                'null' => true,
                'default' => null,
                'limit' => 1,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'dt_message',
            ])
            ->create();
        $this->table('system_user', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('name', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->addColumn('login', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'name',
            ])
            ->addColumn('password', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'login',
            ])
            ->addColumn('email', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'password',
            ])
            ->addColumn('frontpage_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'email',
            ])
            ->addColumn('system_unit_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'frontpage_id',
            ])
            ->addColumn('active', 'char', [
                'null' => true,
                'default' => null,
                'limit' => 1,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'system_unit_id',
            ])
            ->addIndex(['frontpage_id'], [
                'name' => 'sys_user_program_idx',
                'unique' => false,
            ])
            ->create();
        $this->table('system_group', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('name', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->create();
        $this->table('system_user_program', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('system_user_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('system_program_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'system_user_id',
            ])
            ->addIndex(['system_program_id'], [
                'name' => 'sys_user_program_program_idx',
                'unique' => false,
            ])
            ->addIndex(['system_user_id'], [
                'name' => 'sys_user_program_user_idx',
                'unique' => false,
            ])
            ->create();
        $this->table('system_user_unit', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('system_user_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('system_unit_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'system_user_id',
            ])
            ->addIndex(['system_user_id'], [
                'name' => 'system_user_id',
                'unique' => false,
            ])
            ->addIndex(['system_unit_id'], [
                'name' => 'system_unit_id',
                'unique' => false,
            ])
            ->create();
        $this->table('tiposervico', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => 'Tipos de Serviços',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->addIndex(['nome'], [
                'name' => 'idx_tiposervico_nome',
                'unique' => false,
            ])
            ->create();
        $this->table('system_document', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('system_user_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('title', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'system_user_id',
            ])
            ->addColumn('description', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'title',
            ])
            ->addColumn('category_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'description',
            ])
            ->addColumn('submission_date', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'category_id',
            ])
            ->addColumn('archive_date', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'submission_date',
            ])
            ->addColumn('filename', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'archive_date',
            ])
            ->create();
        $this->table('system_change_log', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('logdate', 'timestamp', [
                'null' => false,
                'default' => 'current_timestamp()',
                'after' => 'id',
            ])
            ->addColumn('login', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'logdate',
            ])
            ->addColumn('tablename', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'login',
            ])
            ->addColumn('primarykey', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'tablename',
            ])
            ->addColumn('pkvalue', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'primarykey',
            ])
            ->addColumn('operation', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'pkvalue',
            ])
            ->addColumn('columnname', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'operation',
            ])
            ->addColumn('oldvalue', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'columnname',
            ])
            ->addColumn('newvalue', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'oldvalue',
            ])
            ->addColumn('access_ip', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'newvalue',
            ])
            ->addColumn('transaction_id', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'access_ip',
            ])
            ->addColumn('log_trace', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'transaction_id',
            ])
            ->addColumn('session_id', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_trace',
            ])
            ->addColumn('class_name', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'session_id',
            ])
            ->addColumn('php_sapi', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'class_name',
            ])
            ->addColumn('log_year', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 4,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'php_sapi',
            ])
            ->addColumn('log_month', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_year',
            ])
            ->addColumn('log_day', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_month',
            ])
            ->create();
        $this->table('system_unit', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('name', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->addColumn('connection_name', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'name',
            ])
            ->create();
        $this->table('system_program', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('name', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->addColumn('controller', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'name',
            ])
            ->create();
        $this->table('system_user_group', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('system_user_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('system_group_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'system_user_id',
            ])
            ->addIndex(['system_group_id'], [
                'name' => 'sys_user_group_group_idx',
                'unique' => false,
            ])
            ->addIndex(['system_user_id'], [
                'name' => 'sys_user_group_user_idx',
                'unique' => false,
            ])
            ->create();
        $this->table('system_document_group', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('document_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('system_group_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'document_id',
            ])
            ->create();
        $this->table('system_group_program', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('system_group_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('system_program_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'system_group_id',
            ])
            ->addIndex(['system_program_id'], [
                'name' => 'sys_group_program_program_idx',
                'unique' => false,
            ])
            ->addIndex(['system_group_id'], [
                'name' => 'sys_group_program_group_idx',
                'unique' => false,
            ])
            ->create();
        $this->table('system_access_log', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('sessionid', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->addColumn('login', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'sessionid',
            ])
            ->addColumn('login_time', 'timestamp', [
                'null' => false,
                'default' => 'current_timestamp()',
                'after' => 'login',
            ])
            ->addColumn('login_year', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 4,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'login_time',
            ])
            ->addColumn('login_month', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'login_year',
            ])
            ->addColumn('login_day', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'login_month',
            ])
            ->addColumn('logout_time', 'timestamp', [
                'null' => false,
                'default' => '0000-00-00 00:00:00',
                'after' => 'login_day',
            ])
            ->addColumn('impersonated', 'char', [
                'null' => true,
                'default' => null,
                'limit' => 1,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'logout_time',
            ])
            ->addColumn('access_ip', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'impersonated',
            ])
            ->create();
        $this->table('system_notification', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('system_user_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('system_user_to_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'system_user_id',
            ])
            ->addColumn('subject', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'system_user_to_id',
            ])
            ->addColumn('message', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'subject',
            ])
            ->addColumn('dt_message', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'message',
            ])
            ->addColumn('action_url', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'dt_message',
            ])
            ->addColumn('action_label', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'action_url',
            ])
            ->addColumn('icon', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'action_label',
            ])
            ->addColumn('checked', 'char', [
                'null' => true,
                'default' => null,
                'limit' => 1,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'icon',
            ])
            ->create();
        $this->table('system_document_category', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('name', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->create();
        $this->table('system_sql_log', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('logdate', 'timestamp', [
                'null' => false,
                'default' => 'current_timestamp()',
                'after' => 'id',
            ])
            ->addColumn('login', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'logdate',
            ])
            ->addColumn('database_name', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'login',
            ])
            ->addColumn('sql_command', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'database_name',
            ])
            ->addColumn('statement_type', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'sql_command',
            ])
            ->addColumn('access_ip', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'statement_type',
            ])
            ->addColumn('transaction_id', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'access_ip',
            ])
            ->addColumn('log_trace', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'transaction_id',
            ])
            ->addColumn('session_id', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_trace',
            ])
            ->addColumn('class_name', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'session_id',
            ])
            ->addColumn('php_sapi', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'class_name',
            ])
            ->addColumn('request_id', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'php_sapi',
            ])
            ->addColumn('log_year', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 4,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'request_id',
            ])
            ->addColumn('log_month', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_year',
            ])
            ->addColumn('log_day', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'log_month',
            ])
            ->create();
        $this->table('system_preference', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
            ])
            ->addColumn('value', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'latin1_swedish_ci',
                'encoding' => 'latin1',
                'after' => 'id',
            ])
            ->create();
        $this->table('system_document_user', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'latin1',
                'collation' => 'latin1_swedish_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('document_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('system_user_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'document_id',
            ])
            ->create();
        $this->table('pessoa_imagens', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '32',
            ])
            ->addColumn('arquivo_pessoa_id', 'integer', [
                'null' => false,
                'limit' => '32',
                'after' => 'id',
            ])
            ->addColumn('arquivo_imagem', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'arquivo_pessoa_id',
            ])
            ->create();
        $this->table('tipoarquivo', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->addColumn('liberacao', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'nome',
            ])
            ->addIndex(['nome'], [
                'name' => 'idx_tipoarq_nome',
                'unique' => false,
            ])
            ->addIndex(['liberacao'], [
                'name' => 'tipoarquivo_liberacao_IDX',
                'unique' => false,
            ])
            ->create();
        $this->table('veiculos', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
                'identity' => 'enable',
            ])
            ->addColumn('placa', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 7,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->addColumn('nome_proprietario', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 60,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'placa',
            ])
            ->addColumn('uf', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 4,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'nome_proprietario',
            ])
            ->addColumn('tipo', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'uf',
            ])
            ->addColumn('combustivel', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'tipo',
            ])
            ->addColumn('marca_modelo', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'combustivel',
            ])
            ->addColumn('marca', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'marca_modelo',
            ])
            ->addColumn('cor', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'marca',
            ])
            ->addColumn('ano_fabricacao', 'year', [
                'null' => true,
                'default' => null,
                'limit' => 4,
                'after' => 'cor',
            ])
            ->addColumn('municipio', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'ano_fabricacao',
            ])
            ->addColumn('chassi', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'municipio',
            ])
            ->addColumn('restricao', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'chassi',
            ])
            ->addColumn('renavam', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'restricao',
            ])
            ->addColumn('id_seguradora', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'renavam',
            ])
            ->addColumn('bo_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id_seguradora',
            ])
            ->addColumn('bo_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'bo_dec',
            ])
            ->addColumn('bo_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'bo_rec',
            ])
            ->addColumn('data_cadastro', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'bo_dev',
            ])
            ->addColumn('sinistro', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'data_cadastro',
            ])
            ->addColumn('apolice', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'sinistro',
            ])
            ->addColumn('restricao02', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'apolice',
            ])
            ->addColumn('restricao03', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'restricao02',
            ])
            ->addColumn('restricao04', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'restricao03',
            ])
            ->addColumn('motor', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 15,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'restricao04',
            ])
            ->create();
        $this->table('processo', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
            ])
            ->addColumn('id_vei', 'biginteger', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'id',
            ])
            ->addColumn('id_seg', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id_vei',
            ])
            ->addColumn('representante', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id_seg',
            ])
            ->addColumn('nome_segurado', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 60,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'representante',
            ])
            ->addColumn('uf', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'nome_segurado',
            ])
            ->addColumn('tipo', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'uf',
            ])
            ->addColumn('placa', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 7,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'tipo',
            ])
            ->addColumn('chassi', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'placa',
            ])
            ->addColumn('marca_modelo', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'chassi',
            ])
            ->addColumn('marca', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'marca_modelo',
            ])
            ->addColumn('ano', 'year', [
                'null' => true,
                'default' => null,
                'limit' => 4,
                'after' => 'marca',
            ])
            ->addColumn('cor', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'ano',
            ])
            ->addColumn('motor', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'cor',
            ])
            ->addColumn('renavam', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'motor',
            ])
            ->addColumn('sinistro', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'renavam',
            ])
            ->addColumn('apolice', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'sinistro',
            ])
            ->addColumn('combustivel', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'apolice',
            ])
            ->addColumn('restricao', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'combustivel',
            ])
            ->addColumn('tipo_servico_dec', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'restricao',
            ])
            ->addColumn('tipo_ocorrencia_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'tipo_servico_dec',
            ])
            ->addColumn('data_dec', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'tipo_ocorrencia_dec',
            ])
            ->addColumn('bo_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'data_dec',
            ])
            ->addColumn('cidade_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'bo_dec',
            ])
            ->addColumn('uf_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'cidade_dec',
            ])
            ->addColumn('informante_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 60,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'uf_dec',
            ])
            ->addColumn('ddd_informante_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 60,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'informante_dec',
            ])
            ->addColumn('fone_informante_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 60,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'ddd_informante_dec',
            ])
            ->addColumn('dp_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'fone_informante_dec',
            ])
            ->addColumn('cidade_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'dp_dec',
            ])
            ->addColumn('uf_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'cidade_rec',
            ])
            ->addColumn('data_rec', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'uf_rec',
            ])
            ->addColumn('bo_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'data_rec',
            ])
            ->addColumn('dp_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'bo_rec',
            ])
            ->addColumn('cidade_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'dp_rec',
            ])
            ->addColumn('uf_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'cidade_dev',
            ])
            ->addColumn('dp_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 60,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'uf_dev',
            ])
            ->addColumn('chassi_adulterado_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'dp_dev',
            ])
            ->addColumn('data_entrega_dev', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'chassi_adulterado_dev',
            ])
            ->addColumn('bo_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'data_entrega_dev',
            ])
            ->addColumn('telefone_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'bo_dev',
            ])
            ->addColumn('responsavel_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 60,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'telefone_dev',
            ])
            ->addColumn('nota_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'responsavel_dev',
            ])
            ->addColumn('local_entrega_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 500,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'nota_dev',
            ])
            ->addColumn('tipo_liberacao_dev', 'integer', [
                'null' => true,
                'default' => '1',
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'local_entrega_dev',
            ])
            ->addColumn('obs_dev', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 500,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'tipo_liberacao_dev',
            ])
            ->addColumn('data_cadastro', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'obs_dev',
            ])
            ->addColumn('restricao02', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'data_cadastro',
            ])
            ->addColumn('restricao03', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'restricao02',
            ])
            ->addColumn('restricao04', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'restricao03',
            ])
            ->addColumn('municipio', 'string', [
                'null' => true,
                'default' => '',
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'restricao04',
            ])
            ->addColumn('obs_dec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 500,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'municipio',
            ])
            ->addColumn('obs_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 500,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'obs_dec',
            ])
            ->addColumn('condChassi', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 400,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'obs_rec',
            ])
            ->addColumn('fone_informante_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'condChassi',
            ])
            ->addColumn('ddd_informante_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 4,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'fone_informante_rec',
            ])
            ->addColumn('responsavel_rec', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 255,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'ddd_informante_rec',
            ])
            ->addColumn('condMotor', 'string', [
                'null' => false,
                'limit' => 400,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'responsavel_rec',
            ])
            ->addColumn('usuario', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'condMotor',
            ])
            ->addColumn('liberador', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'usuario',
            ])
            ->addColumn('gestor', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'liberador',
            ])
            ->addColumn('processo_origem', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'gestor',
            ])
            ->addColumn('processo_reintegracao', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'processo_origem',
            ])
            ->addColumn('placa_aplicada', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'processo_reintegracao',
            ])
            ->create();
        $this->table('status', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('statu', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 500,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->addColumn('prazo', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'statu',
            ])
            ->addColumn('email_cobranca', 'text', [
                'null' => true,
                'default' => null,
                'limit' => 65535,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'prazo',
            ])
            ->addColumn('email_liberador', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'email_cobranca',
            ])
            ->addColumn('email_seguradora', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'email_liberador',
            ])
            ->addColumn('status_final', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'email_seguradora',
            ])
            ->create();
        $this->table('processo_arq', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id_arq', 'integer', [
                'null' => false,
                'limit' => '10',
                'signed' => false,
            ])
            ->addColumn('id_processo', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id_arq',
            ])
            ->addColumn('data_arq', 'timestamp', [
                'null' => false,
                'default' => '0000-00-00 00:00:00',
                'after' => 'id_processo',
            ])
            ->addColumn('usuario', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'data_arq',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'usuario',
            ])
            ->addColumn('tipoarq_id', 'integer', [
                'null' => true,
                'default' => '999',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'nome',
            ])
            ->addColumn('assinado', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'tipoarq_id',
            ])
            ->addColumn('token', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'assinado',
            ])
            ->addColumn('hash', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'token',
            ])
            ->create();
        $this->table('recibodetalhe', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('recibo_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 200,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'recibo_id',
            ])
            ->addColumn('valor', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'nome',
            ])
            ->create();
        $this->table('tipolancamento', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 50,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->create();
        $this->table('prococor', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '32',
            ])
            ->addColumn('id_processo', 'integer', [
                'null' => false,
                'limit' => '4',
                'after' => 'id',
            ])
            ->addColumn('usuario', 'string', [
                'null' => false,
                'limit' => 50,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id_processo',
            ])
            ->addColumn('data_ocor', 'timestamp', [
                'null' => false,
                'default' => 'current_timestamp()',
                'after' => 'usuario',
            ])
            ->addColumn('historico', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'data_ocor',
            ])
            ->create();
        $this->table('textodevolucao', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => 'Texto Carta Devolução',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->addColumn('texto', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'nome',
            ])
            ->addIndex(['nome'], [
                'name' => 'idx_textodevolucao_nome',
                'unique' => false,
            ])
            ->create();
        $this->table('textos', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '10',
                'signed' => false,
                'identity' => 'enable',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'id',
            ])
            ->addColumn('texto', 'text', [
                'null' => false,
                'limit' => MysqlAdapter::TEXT_LONG,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'nome',
            ])
            ->create();
        $this->table('titulo', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('pessoa_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('tipolancamento_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'pessoa_id',
            ])
            ->addColumn('data_entrada', 'date', [
                'null' => false,
                'after' => 'tipolancamento_id',
            ])
            ->addColumn('data_vencimento', 'date', [
                'null' => false,
                'after' => 'data_entrada',
            ])
            ->addColumn('data_emissao', 'date', [
                'null' => false,
                'after' => 'data_vencimento',
            ])
            ->addColumn('valor', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'data_emissao',
            ])
            ->addColumn('saldo', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'valor',
            ])
            ->addColumn('numero', 'string', [
                'null' => false,
                'limit' => 20,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'saldo',
            ])
            ->addColumn('parcela', 'integer', [
                'null' => false,
                'default' => '1',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'numero',
            ])
            ->addColumn('pagar_receber', 'string', [
                'null' => false,
                'default' => 'P',
                'limit' => 1,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'parcela',
            ])
            ->addColumn('dc', 'string', [
                'null' => false,
                'default' => 'C',
                'limit' => 1,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'pagar_receber',
            ])
            ->addColumn('tipodoc', 'char', [
                'null' => true,
                'default' => null,
                'limit' => 1,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'dc',
            ])
            ->addColumn('processo_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'tipodoc',
            ])
            ->addColumn('observacao', 'text', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::TEXT_LONG,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'processo_id',
            ])
            ->addIndex(['pessoa_id'], [
                'name' => 'fk_tit_pessoa',
                'unique' => false,
            ])
            ->addIndex(['tipolancamento_id'], [
                'name' => 'fk_tit_tipolan',
                'unique' => false,
            ])
            ->addIndex(['numero', 'tipolancamento_id'], [
                'name' => 'idx_titulo_numero',
                'unique' => false,
            ])
            ->addIndex(['tipodoc', 'numero'], [
                'name' => 'idx_tit_tipo',
                'unique' => false,
            ])
            ->create();
        $this->table('recibos', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8',
                'collation' => 'utf8_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('pessoa_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('processo_id', 'biginteger', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'pessoa_id',
            ])
            ->addColumn('data_emissao', 'date', [
                'null' => false,
                'after' => 'processo_id',
            ])
            ->addColumn('valor_recibo', 'double', [
                'null' => false,
                'after' => 'data_emissao',
            ])
            ->addColumn('status', 'string', [
                'null' => true,
                'default' => 'Ativo',
                'limit' => 10,
                'collation' => 'utf8_general_ci',
                'encoding' => 'utf8',
                'after' => 'valor_recibo',
            ])
            ->create();
        $this->table('comprovante', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('id_processo', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'id',
            ])
            ->addColumn('id_seg', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id_processo',
            ])
            ->addColumn('PlacaVeiculo', 'string', [
                'null' => false,
                'limit' => 7,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id_seg',
            ])
            ->addColumn('ValorTotal', 'string', [
                'null' => false,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'PlacaVeiculo',
            ])
            ->addColumn('Status', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 9,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'ValorTotal',
            ])
            ->addColumn('Data_processo', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'Status',
            ])
            ->addColumn('Data_Atualizao', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'Data_processo',
            ])
            ->create();
        $this->table('mod_empresa', [
                'id' => false,
                'primary_key' => ['emp_id'],
                'engine' => 'MyISAM',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('emp_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('emp_razao_social', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_id',
            ])
            ->addColumn('emp_nome_fantasia', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_razao_social',
            ])
            ->addColumn('emp_telefone', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_nome_fantasia',
            ])
            ->addColumn('emp_celular', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 14,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_telefone',
            ])
            ->addColumn('emp_endereco', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_celular',
            ])
            ->addColumn('emp_complemento', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_endereco',
            ])
            ->addColumn('emp_cep', 'string', [
                'null' => false,
                'limit' => 10,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_complemento',
            ])
            ->addColumn('emp_cnpj', 'string', [
                'null' => false,
                'limit' => 18,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_cep',
            ])
            ->addColumn('emp_numero', 'string', [
                'null' => false,
                'limit' => 6,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_cnpj',
            ])
            ->addColumn('emp_bairro', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_numero',
            ])
            ->addColumn('emp_cidade', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_bairro',
            ])
            ->addColumn('emp_estado', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_cidade',
            ])
            ->addColumn('emp_logo', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_estado',
            ])
            ->addColumn('emp_email', 'string', [
                'null' => false,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_logo',
            ])
            ->addColumn('emp_dominio', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_email',
            ])
            ->addColumn('emp_status', 'char', [
                'null' => false,
                'default' => 'a',
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emp_dominio',
            ])
            ->create();
        $this->table('mod_clientes', [
                'id' => false,
                'primary_key' => ['img_id'],
                'engine' => 'MyISAM',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('img_id', 'integer', [
                'null' => false,
                'limit' => '10',
                'identity' => 'enable',
            ])
            ->addColumn('img_prioridade', 'integer', [
                'null' => false,
                'default' => '10',
                'limit' => '1',
                'after' => 'img_id',
            ])
            ->addColumn('img_imagem', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'img_prioridade',
            ])
            ->addColumn('img_dt_cadastro', 'timestamp', [
                'null' => false,
                'default' => 'current_timestamp()',
                'after' => 'img_imagem',
            ])
            ->addColumn('img_status', 'char', [
                'null' => false,
                'default' => 'a',
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => 'a = ativo; i = inativo',
                'after' => 'img_dt_cadastro',
            ])
            ->addColumn('adm_id_fk', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'comment' => 'Ref: mod_admin',
                'after' => 'img_status',
            ])
            ->create();
        $this->table('pessoa', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '32',
            ])
            ->addColumn('tipo_pessoa', 'char', [
                'null' => false,
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->addColumn('documento', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 18,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'tipo_pessoa',
            ])
            ->addColumn('rg_ie', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'documento',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'rg_ie',
            ])
            ->addColumn('email', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'nome',
            ])
            ->addColumn('data_nascimento', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'email',
            ])
            ->addColumn('rua', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 200,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'data_nascimento',
            ])
            ->addColumn('numero', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'rua',
            ])
            ->addColumn('complemento', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'numero',
            ])
            ->addColumn('bairro', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'complemento',
            ])
            ->addColumn('cep', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'bairro',
            ])
            ->addColumn('cidade', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'cep',
            ])
            ->addColumn('uf', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'cidade',
            ])
            ->addColumn('agenda', 'boolean', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'uf',
            ])
            ->addColumn('observacao', 'text', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::TEXT_LONG,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'agenda',
            ])
            ->addColumn('contato', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'observacao',
            ])
            ->addColumn('liberador', 'integer', [
                'null' => true,
                'default' => '1',
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'contato',
            ])
            ->addColumn('usuario', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'liberador',
            ])
            ->addColumn('apelido', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'usuario',
            ])
            ->addColumn('seguradora', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'apelido',
            ])
            ->create();
        $this->table('banco', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '32',
            ])
            ->addColumn('codigo', 'integer', [
                'null' => false,
                'limit' => '4',
                'after' => 'id',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'codigo',
            ])
            ->create();
        $this->table('contacorrente', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('banco_id', 'integer', [
                'null' => false,
                'limit' => '4',
                'after' => 'id',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'banco_id',
            ])
            ->addColumn('agencia', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'nome',
            ])
            ->addColumn('numero', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'agencia',
            ])
            ->addColumn('tipo', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => '1',
                'after' => 'numero',
            ])
            ->addColumn('chave', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'tipo',
            ])
            ->addColumn('debito', 'decimal', [
                'null' => true,
                'default' => '0.00',
                'precision' => '15',
                'scale' => '2',
                'after' => 'chave',
            ])
            ->addColumn('credito', 'decimal', [
                'null' => true,
                'default' => '0.00',
                'precision' => '15',
                'scale' => '2',
                'after' => 'debito',
            ])
            ->addColumn('ativo', 'integer', [
                'null' => true,
                'default' => '0',
                'limit' => MysqlAdapter::INT_SMALL,
                'after' => 'credito',
            ])
            ->addColumn('data_fechamento', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'ativo',
            ])
            ->create();
        $this->table('pessoa_fone', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '32',
            ])
            ->addColumn('fone_pessoa_id', 'integer', [
                'null' => false,
                'limit' => '32',
                'after' => 'id',
            ])
            ->addColumn('fone_tipo', 'integer', [
                'null' => false,
                'limit' => '32',
                'after' => 'fone_pessoa_id',
            ])
            ->addColumn('fone_numero', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'fone_tipo',
            ])
            ->create();
        $this->table('arquivos', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
            ])
            ->addColumn('antigo_nome_arquivo', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 64,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->addColumn('novo_nome_arquivo', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 64,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'antigo_nome_arquivo',
            ])
            ->addColumn('placa', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 7,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'novo_nome_arquivo',
            ])
            ->addColumn('processo', 'biginteger', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'placa',
            ])
            ->addColumn('data', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'processo',
            ])
            ->create();
        $this->table('mod_admin', [
                'id' => false,
                'primary_key' => ['adm_id'],
                'engine' => 'MyISAM',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('adm_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'identity' => 'enable',
            ])
            ->addColumn('adm_nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'adm_id',
            ])
            ->addColumn('adm_email', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'adm_nome',
            ])
            ->addColumn('adm_senha', 'string', [
                'null' => false,
                'limit' => 32,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'adm_email',
            ])
            ->addColumn('adm_ult_acesso', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'adm_senha',
            ])
            ->addColumn('adm_status', 'char', [
                'null' => false,
                'default' => 'a',
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => 'a = ativo; i = inativo;',
                'after' => 'adm_ult_acesso',
            ])
            ->create();
        $this->table('empresa', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '32',
            ])
            ->addColumn('cnpj', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'cnpj',
            ])
            ->addColumn('datainicio', 'date', [
                'null' => false,
                'after' => 'nome',
            ])
            ->addColumn('ativa', 'boolean', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'datainicio',
            ])
            ->addColumn('cep', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 8,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'ativa',
            ])
            ->addColumn('rua', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'cep',
            ])
            ->addColumn('numero', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'rua',
            ])
            ->addColumn('complemento', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'numero',
            ])
            ->addColumn('bairro', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'complemento',
            ])
            ->addColumn('cidade', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'bairro',
            ])
            ->addColumn('uf', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'cidade',
            ])
            ->addColumn('email', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 40,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'uf',
            ])
            ->addColumn('fone', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'email',
            ])
            ->addColumn('contato', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'fone',
            ])
            ->addColumn('portaria', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'contato',
            ])
            ->addColumn('numero_os', 'double', [
                'null' => true,
                'default' => null,
                'after' => 'portaria',
            ])
            ->addColumn('numero_recibo', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => '32',
                'after' => 'numero_os',
            ])
            ->addColumn('logo', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'numero_recibo',
            ])
            ->create();
        $this->table('mensagens', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
            ])
            ->addColumn('nome', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->addColumn('email', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'nome',
            ])
            ->addColumn('telefone', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 10,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'email',
            ])
            ->addColumn('nextel', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'telefone',
            ])
            ->addColumn('cidade', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 45,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'nextel',
            ])
            ->addColumn('uf', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 2,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'cidade',
            ])
            ->addColumn('msg', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 500,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'uf',
            ])
            ->addColumn('datahora', 'datetime', [
                'null' => false,
                'after' => 'msg',
            ])
            ->create();
        $this->table('parceiros', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '10',
            ])
            ->addColumn('nome', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->create();
        $this->table('mod_quem_somos', [
                'id' => false,
                'primary_key' => ['qs_id'],
                'engine' => 'MyISAM',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('qs_id', 'integer', [
                'null' => false,
                'limit' => '10',
                'identity' => 'enable',
            ])
            ->addColumn('qs_conteudo', 'text', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::TEXT_MEDIUM,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'qs_id',
            ])
            ->addColumn('qs_status', 'char', [
                'null' => false,
                'default' => 'a',
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => 'a = ativo; i = inativo',
                'after' => 'qs_conteudo',
            ])
            ->create();
        $this->table('login', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('l', 'string', [
                'null' => false,
                'limit' => 45,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->addColumn('s', 'string', [
                'null' => false,
                'limit' => 45,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'l',
            ])
            ->addColumn('tipo', 'char', [
                'null' => true,
                'default' => null,
                'limit' => 3,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 's',
            ])
            ->create();
        $this->table('hstatus', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
            ])
            ->addColumn('id_status', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('id_processo', 'biginteger', [
                'null' => false,
                'limit' => MysqlAdapter::INT_BIG,
                'after' => 'id_status',
            ])
            ->addColumn('representante', 'string', [
                'null' => false,
                'limit' => 45,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id_processo',
            ])
            ->addColumn('data_cadastro', 'datetime', [
                'null' => true,
                'default' => null,
                'after' => 'representante',
            ])
            ->create();
        $this->table('cheque', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '32',
            ])
            ->addColumn('contacorrente_id', 'integer', [
                'null' => false,
                'limit' => '32',
                'after' => 'id',
            ])
            ->addColumn('tipolancamento_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => '32',
                'after' => 'contacorrente_id',
            ])
            ->addColumn('data_emissao', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'tipolancamento_id',
            ])
            ->addColumn('numero', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => '32',
                'after' => 'data_emissao',
            ])
            ->addColumn('data_compensacao', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'numero',
            ])
            ->addColumn('valor', 'decimal', [
                'null' => true,
                'default' => null,
                'precision' => '15',
                'scale' => '2',
                'after' => 'data_compensacao',
            ])
            ->addColumn('favorecido', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'valor',
            ])
            ->addColumn('observacao', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 200,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'favorecido',
            ])
            ->addColumn('cancelado', 'boolean', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'observacao',
            ])
            ->addColumn('emitido', 'boolean', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'cancelado',
            ])
            ->addColumn('usuario', 'string', [
                'null' => true,
                'default' => 'master',
                'limit' => 20,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'emitido',
            ])
            ->create();
        $this->table('mod_banner', [
                'id' => false,
                'primary_key' => ['ban_id'],
                'engine' => 'MyISAM',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('ban_id', 'integer', [
                'null' => false,
                'limit' => '10',
                'identity' => 'enable',
            ])
            ->addColumn('ban_prioridade', 'integer', [
                'null' => false,
                'default' => '5',
                'limit' => '1',
                'after' => 'ban_id',
            ])
            ->addColumn('ban_imagem', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'ban_prioridade',
            ])
            ->addColumn('ban_info_link', 'char', [
                'null' => false,
                'default' => 'n',
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => 's=sim; n=nao',
                'after' => 'ban_imagem',
            ])
            ->addColumn('ban_link', 'text', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::TEXT_MEDIUM,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'ban_info_link',
            ])
            ->addColumn('ban_titulo', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 255,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'ban_link',
            ])
            ->addColumn('ban_dt_inicio', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'ban_titulo',
            ])
            ->addColumn('ban_dt_final', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'ban_dt_inicio',
            ])
            ->addColumn('ban_dt_cadastro', 'timestamp', [
                'null' => false,
                'default' => 'current_timestamp()',
                'after' => 'ban_dt_final',
            ])
            ->addColumn('ban_status', 'char', [
                'null' => false,
                'default' => 'a',
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'comment' => 'a = ativo; i = inativo',
                'after' => 'ban_dt_cadastro',
            ])
            ->addColumn('adm_id_fk', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'comment' => 'Ref: mod_admin',
                'after' => 'ban_status',
            ])
            ->addColumn('ban_tipo', 'enum', [
                'null' => false,
                'default' => '_blank',
                'limit' => 6,
                'values' => ['_self', '_blank'],
                'comment' => '_left = interno; _blank = externo',
                'after' => 'adm_id_fk',
            ])
            ->create();
        $this->table('movimentotitulo', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('data_movimento', 'date', [
                'null' => true,
                'default' => null,
                'after' => 'id',
            ])
            ->addColumn('titulo_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'data_movimento',
            ])
            ->addColumn('caixa_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'titulo_id',
            ])
            ->addColumn('cheque_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'caixa_id',
            ])
            ->addColumn('tipolancamento_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'cheque_id',
            ])
            ->addColumn('dc', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'tipolancamento_id',
            ])
            ->addColumn('valor', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'dc',
            ])
            ->addColumn('processo_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'valor',
            ])
            ->addColumn('usuario', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'processo_id',
            ])
            ->addColumn('observacao', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 200,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'usuario',
            ])
            ->create();
        $this->table('calendario', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '10',
                'signed' => false,
            ])
            ->addColumn('hora_inicio', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->addColumn('hora_final', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'hora_inicio',
            ])
            ->addColumn('color', 'text', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::TEXT_MEDIUM,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'hora_final',
            ])
            ->addColumn('title', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'color',
            ])
            ->addColumn('description', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 200,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'title',
            ])
            ->create();
        $this->table('despesa', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
            ])
            ->addColumn('id_comprovante', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'id',
            ])
            ->addColumn('descricao', 'string', [
                'null' => false,
                'limit' => 200,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id_comprovante',
            ])
            ->addColumn('valor', 'decimal', [
                'null' => false,
                'precision' => '12',
                'scale' => '2',
                'after' => 'descricao',
            ])
            ->create();
        $this->table('caixa', [
                'id' => false,
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('id', 'integer', [
                'null' => false,
                'limit' => '32',
                'signed' => false,
            ])
            ->addColumn('contacorrente_id', 'integer', [
                'null' => false,
                'limit' => '32',
                'after' => 'id',
            ])
            ->addColumn('tipolancamento_id', 'integer', [
                'null' => false,
                'limit' => '32',
                'after' => 'contacorrente_id',
            ])
            ->addColumn('data_movimento', 'date', [
                'null' => false,
                'after' => 'tipolancamento_id',
            ])
            ->addColumn('dc', 'char', [
                'null' => false,
                'default' => 'C',
                'limit' => 1,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'data_movimento',
            ])
            ->addColumn('valor', 'decimal', [
                'null' => false,
                'precision' => '15',
                'scale' => '2',
                'after' => 'dc',
            ])
            ->addColumn('saldo', 'decimal', [
                'null' => true,
                'default' => '0.00',
                'precision' => '15',
                'scale' => '2',
                'after' => 'valor',
            ])
            ->addColumn('compensado', 'boolean', [
                'null' => false,
                'limit' => MysqlAdapter::INT_TINY,
                'after' => 'saldo',
            ])
            ->addColumn('controle', 'string', [
                'null' => true,
                'default' => null,
                'limit' => 50,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'compensado',
            ])
            ->addColumn('historico', 'string', [
                'null' => false,
                'limit' => 500,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'controle',
            ])
            ->addColumn('usuario', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_general_ci',
                'encoding' => 'utf8mb4',
                'after' => 'historico',
            ])
            ->addColumn('operacao_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'usuario',
            ])
            ->addColumn('pessoa_id', 'integer', [
                'null' => true,
                'default' => null,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'operacao_id',
            ])
            ->create();
    }
}
