#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#define MAX_REC_CNT 1000

struct outputRecord
{
	char *id;
	char *category;
};
typedef struct outputRecord outputRec;

void handle_nl(char *pat);
int rec_get(char *fileName, char *delimeter, char *rec_list[]);
outputRec findRecStruct(char *recString);
char *getFieldValue(char *recString, char *fieldStr, char *fieldEnd);

int main(int argc, char *argv[])
{
	char ch;

	char *inFileName = NULL;
	char *outFileName = NULL;
	char *recDelimeter = NULL;

	FILE *outFp;

	char *recList[MAX_REC_CNT];
	int recAmount=0;
	int i=0;
	outputRec *recData;

	while( (ch=getopt(argc, argv, "f:d:o:"))!=-1 ){
		switch(ch){
			case 'f':
				inFileName = (char *)malloc( (strlen(optarg)+1) * sizeof(char) );
				strcpy(inFileName, optarg);
				printf("[1] the file name:%s\n", inFileName);
				break;
			case 'd':
				recDelimeter = (char *)malloc( (strlen(optarg)+1) * sizeof(char) );
				handle_nl(optarg);
				strcpy(recDelimeter, optarg);
				printf("[2] record delimeter:%s\n", recDelimeter);
				break;
			case 'o':
				outFileName = (char *)malloc( (strlen(optarg)+1) * sizeof(char) );
				strcpy(outFileName, optarg);
				printf("[1] the file name:%s\n", outFileName);
				break;
			deafult:
				break;
		}
	}

	if(inFileName == NULL){
		printf("{1} no input file name.\n");
		exit(0);
	}
	if(recDelimeter == NULL){
		printf("{2} no record delimeter.\n");
		exit(0);
	}

	recAmount = rec_get(inFileName, recDelimeter, recList);
	printf("recAmount:%d\n", recAmount);
	recData = (outputRec *)malloc(sizeof(outputRec)*recAmount);
	for(i=0; i<recAmount; i++){
		//printf("%d*****\n%s\n*****\n", i, recList[i]);
		recData[i] = findRecStruct(recList[i]);
		printf("theRec.id: %s\n", recData[i].id);
		printf("theRec.category: %s\n", recData[i].category);
	}

	if( (outFp=fopen(outFileName, "w")) == NULL ){
		printf("{4} output file can't open.\n");
	}
	for(i=0; i<recAmount; i++){
		fwrite(recDelimeter, 1, strlen(recDelimeter), outFp);
		fwrite("@vid:", 1, 5, outFp);
		fwrite(recData[i].id, 1, strlen(recData[i].id), outFp);
		fwrite("\n@category:", 1, 11, outFp);
		fwrite(recData[i].category, 1, strlen(recData[i].category), outFp);
		fwrite("\n", 1, 1, outFp);
		fwrite("@click_count:0\n@like_count:0\n@disliek_count:0\n@cached:0\n", 1, 56, outFp);
	}
	fclose(outFp);
	return 0;
}

outputRec findRecStruct(char *recString)
{
	outputRec theRec;
	char id_field[7]  = "\n@VID:";
	char cat_field[12] = "\n@category:";
	char field_end[3]  = "\n@";

	theRec.id = getFieldValue(recString, id_field, field_end);
	theRec.category = getFieldValue(recString, cat_field, field_end);
	//printf("theRec.id: %s\n", theRec.id);
	//printf("theRec.category: %s\n", theRec.category);
	return theRec;
}

char *getFieldValue(char *recString, char *fieldStr, char *fieldEnd)
{
	int fieldStr_len = strlen(fieldStr);
	int fieldEnd_len = strlen(fieldEnd);
	char *temp = NULL;
	char *temp_nxt = NULL;
	char *value = NULL;
	int value_len = 0;

	//printf("*****\n%s\n*****\n\n", recString);

	temp = memmem(recString, strlen(recString), fieldStr, fieldStr_len);
	if(temp==NULL){
		return NULL;
	}
	temp_nxt = memmem(temp+fieldStr_len, strlen(temp)-fieldStr_len, fieldEnd, fieldEnd_len);
	if(temp_nxt!=NULL){
		value_len = (temp_nxt - temp) - fieldStr_len;
	}
	else{
		value_len = strlen(temp) - fieldStr_len;
	}
	value = (char *)malloc(sizeof(char) * (value_len+1));
	memset(value,'\0',value_len+1);
	memcpy(value, temp+fieldStr_len, value_len);
	//printf("=====value:%s\n\n=====\n",value);
	return value;
}

int rec_get(char *fileName, char *delimeter, char *rec_list[])
{
	FILE *inFp;
	fpos_t fileStart_pos;
	fpos_t fileEnd_pos;
	int fileSize;

	int deli_len = strlen(delimeter);

	char *buffer = NULL;
	char *temp = NULL;
	char *temp_nxt = NULL;
	int leftSize = 0;
	int recSize = 0;
	//char *recBuf = NULL;
	//char *rec_list[MAX_REC_CNT];
	int recCnt = 0;


	if( (inFp = fopen(fileName, "r")) == NULL){
		printf("{3} file open failed.");
		exit(0) ;
	}
	//printf("file open success");
	fgetpos(inFp, &fileStart_pos);
	fseek(inFp, 0, SEEK_END);
	fgetpos(inFp, &fileEnd_pos);
	fileSize = (int)(fileEnd_pos-fileStart_pos);
	printf("filesize=%d\n",fileSize);
	rewind(inFp);

	buffer = (char *)malloc(sizeof(char) * fileSize);
	memset(buffer,'\0', fileSize);
	fread(buffer, 1, fileSize, inFp);
	temp = memmem(buffer, fileSize, delimeter, deli_len);
	leftSize = fileSize;
	while(temp != NULL)
	{
		temp_nxt = memmem(temp+deli_len, leftSize-deli_len, delimeter, deli_len);
		if(temp_nxt == NULL){
			recSize = strlen(temp);
			/*recBuf = (char *)malloc(sizeof(char) * (recSize+1) );
			memcpy(recBuf, temp, recSize);
			printf("*****\n%s\n*****\n", recBuf);*/
			rec_list[recCnt] = (char *)malloc(sizeof(char) * (recSize+1) );
			memset(rec_list[recCnt],'\0', recSize+1);
			memcpy(rec_list[recCnt], temp, recSize);
			//printf("%d*****\n%s\n*****\n", recCnt, rec_list[recCnt]);
			break;
		}
		recSize = temp_nxt-temp;
		/*recBuf = (char *)malloc(sizeof(char) * (recSize+1) );
		memcpy(recBuf, temp, recSize);
		printf("*****\n%s\n*****\n", recBuf);*/
		rec_list[recCnt] = (char *)malloc(sizeof(char) * (recSize+1) );
		memset(rec_list[recCnt],'\0', recSize+1);
		memcpy(rec_list[recCnt], temp, recSize);
		//printf("%d*****\n%s\n*****\n", recCnt, rec_list[recCnt]);


		leftSize = leftSize - recSize;
		temp = temp_nxt;
		recCnt++;
	}

	fclose(inFp);
	return recCnt+1;
}

void handle_nl(char *pat)
{
	char *temp_1;
	char *temp_2;

	temp_1 = strstr(pat, "\\n");
	while(temp_1!=NULL){
		temp_2 = temp_1+2;
		temp_1[0] = '\n';
		strcpy(temp_1+1, temp_2);
		temp_1 = strstr(pat, "\\n");
	}
}
