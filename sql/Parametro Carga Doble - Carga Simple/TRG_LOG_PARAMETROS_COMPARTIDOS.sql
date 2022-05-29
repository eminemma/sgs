create or replace TRIGGER TRG_LOG_PARAMETROS_COMPARTIDOS 


after update ON T_PARAMETRO_COMPARTIDO
  FOR EACH ROW

DECLARE
 v_usuario VARCHAR2(20);
BEGIN
 SELECT SYS_CONTEXT ('USERENV', 'CLIENT_IDENTIFIER')  into v_usuario
   FROM DUAL;

 IF UPDATING THEN
     IF :OLD.PARAMETRO = 'CARGADOBLE'  THEN
        INSERT INTO T_LOG_PARAMETROS
          (
          PARAMETRO,
          VALOR_VIEJO,
          VALOR_NUEVO,
          USUARIO
      )
        VALUES
          (
          :OLD.PARAMETRO,
          :OLD.VALOR,
          :NEW.VALOR,
          v_usuario);
    
      END IF;
  END IF;
END;