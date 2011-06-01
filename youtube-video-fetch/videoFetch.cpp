/* 
 * File:   videoFetch.cpp
 * Author: flachesis
 * 
 * Created on 2011年4月17日, 上午 12:59
 */

#include "videoFetch.h"

videoFetch::videoFetch(std::set<std::string> *vidSet, std::set<std::string>::iterator start, std::set<std::string>::iterator end) {
    this->vidSet = vidSet;
    this->start = start;
    this->end = end;
    curl_global_init(CURL_GLOBAL_WIN32);
}

videoFetch::videoFetch(const videoFetch& orig) {
}

videoFetch::~videoFetch() {
    curl_global_cleanup();
}

void videoFetch::run(){
    std::ofstream fout;
    std::string fileName;
    for(std::set<std::string>::iterator it = this->start; it != this->end;){
            std::auto_ptr<std::string> htmlContent(this->getHtmlContent(*it));
            if(htmlContent->find("verificationImage") != std::string::npos){
                continue;
            }
            std::auto_ptr<std::vector<std::pair<std::string, std::string> > > urlExtensionVector(htmlParser::getDownloadUrlExtension(*htmlContent));
            if(urlExtensionVector.get() == NULL){
                it++;
                continue;
            }
            for(std::vector<std::pair<std::string, std::string> >::iterator it2 = urlExtensionVector->begin(); it2 != urlExtensionVector->end(); it2++){
                fileName = *it + "." + it2->second;
                fout.open(fileName.c_str(), std::ios_base::out | std::ios_base::trunc | std::ios_base::binary);
                int videoBinData = this->getVideoUsingCurl(it2->first, fout);
                fout.close();
                if(videoBinData != 0){
                    std::remove(fileName.c_str());
                    continue;
                }else{
                    break;
                }
            }
            it++;
    }
}

std::string* videoFetch::getHtmlContent(const std::string &vid){
    std::string *htmlContent = new std::string;
    CURL *curl = curl_easy_init();
    if(curl == NULL){
        return htmlContent;
    }
    std::string url = "http://www.youtube.com/watch?v=" + vid;
    curl_easy_setopt(curl, CURLOPT_URL, url.c_str());
    curl_easy_setopt(curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-TW; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13");
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, htmlContent);
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, curlWriteStringCallBack);
    curl_easy_setopt(curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_easy_setopt(curl, CURLOPT_MAXREDIRS, 5);
    curl_easy_setopt(curl, CURLOPT_LOW_SPEED_LIMIT, 1);
    curl_easy_setopt(curl, CURLOPT_LOW_SPEED_TIME, 120);
    CURLcode res = curl_easy_perform(curl);
    long code;
    curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &code);
    curl_easy_cleanup(curl);
    return htmlContent;
}

int videoFetch::getVideoUsingCurl(const std::string &url, std::ostream &outStream){
    CURL *curl = curl_easy_init();
    if(curl == NULL){
        return -1;
    }
    curl_easy_setopt(curl, CURLOPT_URL, url.c_str());
    curl_easy_setopt(curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-TW; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13");
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &outStream);
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, curlWriteCallBack);
    curl_easy_setopt(curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_easy_setopt(curl, CURLOPT_MAXREDIRS, 5);
    curl_easy_setopt(curl, CURLOPT_LOW_SPEED_LIMIT, 1);
    curl_easy_setopt(curl, CURLOPT_LOW_SPEED_TIME, 120);
    CURLcode res = curl_easy_perform(curl);
    long code;
    curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &code);
    curl_easy_cleanup(curl);
    if(code >= 400){
        return CURLE_HTTP_RETURNED_ERROR;
    }
    return res;
}

size_t videoFetch::curlWriteCallBack( void *ptr, size_t size, size_t nmemb, void *userdata){
    std::ostream *outStream = (std::ostream*)userdata;
    outStream->write((char*)ptr, size * nmemb);
    return (size * nmemb);
}

size_t videoFetch::curlWriteStringCallBack( void *ptr, size_t size, size_t nmemb, void *userdata){
    std::string *stringPointer = (std::string*) userdata;
    stringPointer->append((char*)ptr, size * nmemb);
    return (size * nmemb);
}
