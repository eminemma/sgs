
CREATE TABLE "SGS"."T_HIST_SITUACION_SORTEO" (
    "ID" NUMBER NOT NULL ENABLE, 
	"SITUACION" VARCHAR2(100 CHAR), 
	"SORTEO" NUMBER NOT NULL ENABLE, 
	"ID_JUEGO" NUMBER NOT NULL ENABLE, 
	"ESTADO" VARCHAR2(20 BYTE), 
	"FECHA" DATE DEFAULT SYSDATE,
    CONSTRAINT "T_NOFITICACION_PK" PRIMARY KEY ( "ID" )
);

COMMENT ON COLUMN "SGS"."T_HIST_SITUACION_SORTEO"."ESTADO" IS '''I''=>Iniciado, ''S''=>Suspendido, ''F''=>Finalizado';
   
CREATE SEQUENCE  "SGS"."SEC_HIST_SECUENCIA_SORTEO"  MINVALUE 1 MAXVALUE 9999999999999999999999999999 INCREMENT BY 1 START WITH 1 CACHE 20 NOORDER  NOCYCLE ;


  CREATE OR REPLACE TRIGGER "SGS"."TRG_HIST_SITUACION_SORTEO" 
   before insert on "SGS"."T_HIST_SITUACION_SORTEO" 
   for each row 
begin  
   if inserting then 
      if :NEW."ID" is null then 
         select SEC_HIST_SECUENCIA_SORTEO.nextval into :NEW."ID" from dual; 
      end if; 
   end if; 
end;