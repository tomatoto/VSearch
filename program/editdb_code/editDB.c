#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#define MAXSIZE 1000000

void handle_nl(char *pat);
int findRec(char *fileName, char *delimeter, char *query, char *cntField, int flag);
char *getCount(char *rec, char *recBeg, int recBeg_len, char *q, char *count);
char *editRecord(char *rec, char *recBeg, int recBeg_len, char *q, char *count);
char *editRecord_u(char *rec, char *recBeg, int recBeg_len, char *q, char *count);
char *increField(char *str);
char *decreField(char *str);

int main(int argc, char *argv[])
{
/***** operator *****/
	char ch;

	FILE *fp = NULL;
	char *inFileName = NULL;
	char *rec_deli = NULL;
	char *queryStr = NULL;
	char *countField = NULL;

	int setFlag = 0;
	int getFlag = 0;
	int unsetFlag = 0;
	int exeResult = 0;

	while( (ch=getopt(argc, argv, "f:d:q:c:sug"))!=-1 ){
		switch(ch){
			case 'f':	//-f	input file name
				inFileName = (char *)malloc( (strlen(optarg)+1) * sizeof(char) );
				strcpy(inFileName,optarg);
				//printf("the file name:%s\n", inFileName);
				break;
			case 's':	//set
				setFlag = 1;
				break;
			case 'u':
				unsetFlag = 1;
				break;
			case 'g':	//get
				getFlag = 1;
				break;
			case 'd':	//-d	record delimeter
				rec_deli = (char *)malloc( (strlen(optarg)+1) * sizeof(char) );
				handle_nl(optarg);
				strcpy(rec_deli, optarg);
				//printf("record delimeter:%s\n", rec_deli);
				break;
			case 'q':	//query "@field:value\n"
				queryStr = (char *)malloc( (strlen(optarg)+1) * sizeof(char) );
				handle_nl(optarg);
				strcpy(queryStr, optarg);
				//printf("queryStr:%s\n", queryStr);
				break;
			case 'c':	//the count field to ++
				countField = (char *)malloc( (strlen(optarg)+1) * sizeof(char) );
				strcpy(countField, optarg);
				//printf("countField:%s\n", countField);
				break;
			default:
				break;
		}
	}

	if( inFileName == NULL){
		printf("Error:1, no input file\n");
		exit(0);
	}
	
	if( rec_deli == NULL ){
		printf("Error:2, no delimeter\n");
		exit(0);
	}

	if( queryStr == NULL ){
		printf("Error:3, no queryStr\n");
		exit(0);
	}

	if( countField== NULL ){
		printf("Error:4, no countField\n");
		exit(0);
	}

	if(getFlag == 1 && setFlag ==0 && unsetFlag ==0){
		findRec(inFileName, rec_deli, queryStr, countField, 0);
		return 0;
	}
	else if(getFlag ==0 && setFlag == 1 && unsetFlag ==0){
		exeResult = findRec(inFileName, rec_deli, queryStr, countField, 1);
	}
	else if(getFlag ==0 && setFlag ==0 && unsetFlag ==1){
		exeResult = findRec(inFileName, rec_deli, queryStr, countField, -1);
	}
	else{
		printf("Error:5, duplicate option\n");
		exit(0);
	}

	if(exeResult == 1){
		printf("@result:A\nFile not exist.\n");
	}
	else if(exeResult == 2){
		printf("@result:B\nFile exist, but the record include \"%s\" not exist yet.\n", queryStr);
	}
	else if(exeResult == 4){
		printf("@result:C\nUnexpected error\n");
	}
	else if(exeResult == 3){
		printf("@result:D\nWrong record delimeter.\n");
	}
	else{
		printf("@result:E\nThe record update \"%s\" success.\n", countField);
	}

	return 0;
}

int findRec(char *fileName, char *delimeter, char *query, char *cntField, int flag)
{
	char *buffer = NULL;
	int readBytes = 0;
	FILE *fp = NULL;
	int deli_len = strlen(delimeter);
	int query_len = strlen(query);
	int cntField_len = strlen(cntField);

	char *temp = NULL;
	char *temp_nxt = NULL;
	int left_len = 0;
	int buffer_len = 0;
	char *recBuffer = NULL;
	char *leftBuffer = NULL;
	int readBuf_pos = 0;

	char *cnt_temp = NULL;
	char *cnt_value = NULL;
	char *recToWrite = NULL;
	
	if( (fp = fopen(fileName, "r+")) == NULL)
	{
		return 1;
	}

	fseek(fp, 0, SEEK_SET);
	buffer = (char *)malloc( sizeof(char) * MAXSIZE );
	readBytes = fread(buffer, 1, MAXSIZE, fp);
	//printf("readBytes:%d\n==========\n\n", readBytes);
	if(readBytes == 0){
		return 4;
	}
	//printf("buffer:%s\n",buffer);

	if( (temp = memmem(buffer,readBytes, delimeter, deli_len)) == NULL ){
			return 3;
	}
	left_len = readBytes;
	while( temp != NULL ){
		temp_nxt = memmem(temp+deli_len, left_len-deli_len, delimeter, deli_len);
		if(temp_nxt !=NULL){
			buffer_len = temp_nxt - temp;
		}
		else{
			buffer_len = left_len;
		}

		//printf("buffer_len:%d\n", buffer_len);
		recBuffer = (char *)malloc(sizeof(char) * buffer_len);
		memcpy(recBuffer, temp, buffer_len);
		//printf("---\n%s\n---\n", recBuffer);

		left_len = left_len - buffer_len;
		temp = temp_nxt;

		if( memmem(recBuffer, buffer_len, query, query_len)!=NULL ){
			leftBuffer = (char *)malloc(sizeof(char) * left_len);
			memcpy(leftBuffer, temp, left_len);

			if(flag == 1){
				recToWrite = editRecord(recBuffer, delimeter, deli_len, query, cntField);
				printf("recToWrite:%s\n", recToWrite);

				//printf("ftell:%d\n", ftell(fp));
				fseek(fp, readBuf_pos, SEEK_SET);
				//printf("ftell:%d\n", ftell(fp));
				fwrite(recToWrite, 1, strlen(recToWrite), fp);
				fwrite(leftBuffer, 1,left_len, fp);
				fclose(fp);
				return 0;
			}
			else if(flag == -1){
				recToWrite = editRecord_u(recBuffer, delimeter, deli_len, query, cntField);
				fseek(fp, readBuf_pos, SEEK_SET);
				fwrite(recToWrite, 1, strlen(recToWrite), fp);
				fwrite(leftBuffer, 1,left_len, fp);
				fclose(fp);
				return 0;
			}
			else{
				//printf("!!!!!!!!!!");
				getCount(recBuffer, delimeter, deli_len, query, cntField);
				fclose(fp);
				return 0;
			}
			//}
		}
		readBuf_pos = readBuf_pos + buffer_len;
	}

	fclose(fp);
	return 2;

}

char *getCount(char *rec, char *recBeg, int recBeg_len, char *q, char *count)
{
	char *pure_rec = NULL;
	char *rec_array[10];
	char *temp = NULL;
	char *temp_nxt = NULL;
	int field_num = 0;

	char *return_str = NULL;
	int return_size = 0;

	pure_rec = (char *)malloc( sizeof(char) * strlen(rec) );
	memcpy(pure_rec, rec+recBeg_len, strlen(rec)-recBeg_len);
	//printf("HHHHH\n%s\nHHHHH\n", pure_rec);
	temp = pure_rec;
	return_size = strlen(recBeg);
	while(temp != NULL){
		temp_nxt = memmem(temp, strlen(temp), "\n@", 2);
		if(temp_nxt != NULL){
			rec_array[field_num] = (char *)malloc( sizeof(char) * ((temp_nxt-temp)+1) );
			memcpy(rec_array[field_num], temp, (temp_nxt-temp)+1);
			if(memmem(rec_array[field_num], strlen(rec_array[field_num]), count, strlen(count)) != NULL){
				break;
			}
		}
		else{
			rec_array[field_num] = (char *)malloc( sizeof(char) * strlen(temp) );
			memcpy(rec_array[field_num], temp, strlen(temp));
			if(memmem(rec_array[field_num], strlen(rec_array[field_num]), count, strlen(count)) != NULL){
				break;
			}
		}
		field_num++;
		temp = temp_nxt+1;
	}

	temp = memmem(rec_array[field_num], strlen(rec_array[field_num]), ":", 1);
	return_size = strlen(rec_array[field_num])-(temp-rec_array[field_num]);
	return_str = (char *)malloc(sizeof(char)*return_size);
	memcpy(return_str, temp+1, return_size);

	printf("@count:%s\n", return_str);
	return return_str;
}
char *editRecord_u(char *rec, char *recBeg, int recBeg_len, char *q, char *count)
{
	char *pure_rec = NULL;
	char *rec_array[10];
	char *temp = NULL;
	char *temp_nxt = NULL;
	int field_num = 0;
	int i=0;
	char *return_rec = NULL;
	int return_size = 0;

	//memset(temp, '\0', 100);
	//printf("HHHHH\n%s\nHHHHH\n", rec);
	pure_rec = (char *)malloc( sizeof(char) * strlen(rec) );
	memcpy(pure_rec, rec+recBeg_len, strlen(rec)-recBeg_len);
	//printf("HHHHH\n%s\nHHHHH\n", pure_rec);
	temp = pure_rec;
	return_size = strlen(recBeg);
	while(temp != NULL){
		temp_nxt = memmem(temp, strlen(temp), "\n@", 2);
		if(temp_nxt != NULL){
			rec_array[field_num] = (char *)malloc( sizeof(char) * ((temp_nxt-temp)+1) );
			memcpy(rec_array[field_num], temp, (temp_nxt-temp)+1);
			//printf("===%d===: %s\n", field_num, rec_array[field_num]);
			if(memmem(rec_array[field_num], strlen(rec_array[field_num]), count, strlen(count)) != NULL){
				rec_array[field_num] = decreField(rec_array[field_num]);
			}
			//printf("====%d====:%s\n", field_num, rec_array[field_num]);
			return_size = return_size + strlen(rec_array[field_num]);
		}
		else{
			rec_array[field_num] = (char *)malloc( sizeof(char) * strlen(temp) );
			memcpy(rec_array[field_num], temp, strlen(temp));
			//printf("===%d===: %s\n", field_num, rec_array[field_num]);
			if(memmem(rec_array[field_num], strlen(rec_array[field_num]), count, strlen(count)) != NULL){
				rec_array[field_num] = decreField(rec_array[field_num]);
			}
			//printf("====%d====:%s\n", field_num, rec_array[field_num]);
			return_size = return_size + strlen(rec_array[field_num]);
			break;
		}
		field_num++;
		temp = temp_nxt+1;
	}

	/*for(i=0; i<=field_num; i++){
		if(memmem(rec_array[i], strlen(rec_array[i]), count, strlen(count)) != NULL){
			rec_array[i] = increField(rec_array[i]);
		}
		//printf("====%d====:%s\n", i, rec_array[i]);
		return_size = return_size + strlen(rec_array[i]);
	}*/

	return_rec = (char *)malloc( sizeof(char) * return_size);
	memcpy(return_rec, recBeg, strlen(recBeg));
	for(i=0; i<=field_num; i++){
		strcat(return_rec, rec_array[i]);
	}
	//printf("=============NEW Record:===========\n%s\n===============================\n", return_rec);

	return return_rec;
}

char *editRecord(char *rec, char *recBeg, int recBeg_len, char *q, char *count)
{
	char *pure_rec = NULL;
	char *rec_array[10];
	char *temp = NULL;
	char *temp_nxt = NULL;
	int field_num = 0;
	int i=0;
	char *return_rec = NULL;
	int return_size = 0;

	//memset(temp, '\0', 100);
	//printf("HHHHH\n%s\nHHHHH\n", rec);
	pure_rec = (char *)malloc( sizeof(char) * strlen(rec) );
	memcpy(pure_rec, rec+recBeg_len, strlen(rec)-recBeg_len);
	//printf("HHHHH\n%s\nHHHHH\n", pure_rec);
	temp = pure_rec;
	return_size = strlen(recBeg);
	while(temp != NULL){
		temp_nxt = memmem(temp, strlen(temp), "\n@", 2);
		if(temp_nxt != NULL){
			rec_array[field_num] = (char *)malloc( sizeof(char) * ((temp_nxt-temp)+1) );
			memcpy(rec_array[field_num], temp, (temp_nxt-temp)+1);
			//printf("===%d===: %s\n", field_num, rec_array[field_num]);
			if(memmem(rec_array[field_num], strlen(rec_array[field_num]), count, strlen(count)) != NULL){
				rec_array[field_num] = increField(rec_array[field_num]);
			}
			//printf("====%d====:%s\n", field_num, rec_array[field_num]);
			return_size = return_size + strlen(rec_array[field_num]);
		}
		else{
			rec_array[field_num] = (char *)malloc( sizeof(char) * strlen(temp) );
			memcpy(rec_array[field_num], temp, strlen(temp));
			//printf("===%d===: %s\n", field_num, rec_array[field_num]);
			if(memmem(rec_array[field_num], strlen(rec_array[field_num]), count, strlen(count)) != NULL){
				rec_array[field_num] = increField(rec_array[field_num]);
			}
			//printf("====%d====:%s\n", field_num, rec_array[field_num]);
			return_size = return_size + strlen(rec_array[field_num]);
			break;
		}
		field_num++;
		temp = temp_nxt+1;
	}

	/*for(i=0; i<=field_num; i++){
		if(memmem(rec_array[i], strlen(rec_array[i]), count, strlen(count)) != NULL){
			rec_array[i] = increField(rec_array[i]);
		}
		//printf("====%d====:%s\n", i, rec_array[i]);
		return_size = return_size + strlen(rec_array[i]);
	}*/

	return_rec = (char *)malloc( sizeof(char) * return_size);
	memcpy(return_rec, recBeg, strlen(recBeg));
	for(i=0; i<=field_num; i++){
		strcat(return_rec, rec_array[i]);
	}
	//printf("=============NEW Record:===========\n%s\n===============================\n", return_rec);

	return return_rec;
}

char *decreField(char *str)
{
	char *temp = NULL;
	char field[20] = {'\0'};
	int value = 0;
	char value_str[10] = {'\0'};
	char *return_str = NULL;

	temp = memmem(str, strlen(str), ":", 1);
	if(temp != NULL){
		memcpy(field, str, (temp-str)+1);
		value = atoi(temp+1);
		//printf("==%d\n", value);
		value=value-1;
		//printf("==%d\n", value);
		sprintf(value_str, "%d", value);

		return_str = (char *)malloc(sizeof(char) * strlen(str)+1);
		memset(return_str,'\0', strlen(str)+1);
		strcpy(return_str, field);
		strcat(return_str, value_str);
		strcat(return_str, "\n");

		return return_str;
	}
}
char *increField(char *str)
{
	char *temp = NULL;
	char field[20] = {'\0'};
	int value = 0;
	char value_str[10] = {'\0'};
	char *return_str = NULL;

	temp = memmem(str, strlen(str), ":", 1);
	if(temp != NULL){
		memcpy(field, str, (temp-str)+1);
		value = atoi(temp+1);
		//printf("==%d\n", value);
		value++;
		//printf("==%d\n", value);
		sprintf(value_str, "%d", value);

		return_str = (char *)malloc(sizeof(char) * strlen(str)+1);
		memset(return_str,'\0', strlen(str)+1);
		strcpy(return_str, field);
		strcat(return_str, value_str);
		strcat(return_str, "\n");

		return return_str;
	}
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

