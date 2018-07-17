id	int(11)	NO	PRI	NULL	auto_increment
nome	varchar(255)	YES		NULL	
dta_nacimento	date	YES		NULL	
apelido	varchar(255)	YES		NULL	
tel_fixo	varchar(50)	YES		NULL	
celular	varchar(50)	YES		NULL	
nextel	varchar(255)	YES		NULL	
email	varchar(1000)	YES		NULL	
email_marketing	int(1)	YES		NULL	
banco	varchar(50)	YES		NULL	
agencia	varchar(45)	YES		NULL	
conta	varchar(45)	YES		NULL	
cliente_desde	char(7)	YES		NULL	
endereco	varchar(255)	YES		NULL	
end_complemento	text	YES		NULL	
numero	int(11)	YES		NULL	
bairro	varchar(255)	YES		NULL	
cep	int(8) unsigned zerofill	YES		NULL	
cidade	varchar(255)	YES		NULL	
uf	varchar(2)	YES		NULL	
regiao	varchar(255)	YES		NULL	
ponto_referencia	varchar(255)	YES		NULL	
indicacao	int(1)	YES		NULL	
indicado_por_quem	varchar(255)	YES		NULL	
id_finalidade	int(11)	YES	MUL	NULL	
id_como_encontrou	int(11)	YES	MUL	NULL	
como_entrou_outros	varchar(255)	YES		NULL	
status	int(1)	YES		0	
id_perfil	int(11)	YES		NULL	
id_empreendimento	int(11)	YES	MUL	NULL	
num_latitude	double	YES		NULL	
num_longitude	double	YES		NULL	
id_banco	int(11)	YES		NULL	
cod_pais	varchar(255)	YES		NULL	
id_estado	int(11)	YES		NULL	
id_cidade	int(11)	YES		NULL	
dta_cadastro	datetime	YES		CURRENT_TIMESTAMP	
id_grupo_comissionamento	int(11)	YES	MUL	NULL	
cod_regime_tributario	int(11)	YES		NULL	
cod_regime_pis_cofins	int(11)	YES		NULL	
cod_tipo_empresa	int(11)	YES		NULL	
flg_contribuinte_icms	bit(1)	YES		NULL	
flg_isento_inscricao_estadual	bit(1)	YES		NULL	
flg_contribuinte_ipi	bit(1)	YES		NULL	
num_registro_estrangeiro	varchar(255)	YES		NULL	
cod_zoneamento	int(11)	YES	MUL	NULL	
num_ficha_paciente	varchar(255)	YES		NULL	
flg_tipo	set('usuario','cliente')	YES		NULL	
id_plano_contas_padrao	int(11)	YES		NULL	
dsc_finalidade	varchar(255)	YES		NULL	
flg_cadastro_externo	int(11)	YES		NULL	
flg_ativo	int(11)	YES		1	
id_ref	int(11)	YES		NULL	
id_vendedor_responsavel	int(11)	YES		NULL	
txt_historico_acionamentos	text	YES		NULL	
id_externo	int(11)	YES		NULL	
pessoa_contato	varchar(255)	YES		NULL	
vlr_limite_credito	double	YES		NULL	
num_cartao	varchar(45)	YES		NULL	
flg_excluido	int(11)	YES		0	
id_empreendimento_cliente	int(11)	YES		NULL	
id_ezcommerce	int(11)	YES		NULL	