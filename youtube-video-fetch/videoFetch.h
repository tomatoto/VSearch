/* 
 * File:   videoFetch.h
 * Author: flachesis
 *
 * Created on 2011年4月17日, 上午 12:59
 */

#ifndef VIDEOFETCH_H
#define	VIDEOFETCH_H

#include <set>
#include <istream>
#include <iostream>
#include "htmlParser.h"
#include "curl/curl.h"

class videoFetch {
public:
    videoFetch(std::set<std::string> *vidSet, std::set<std::string>::iterator start, std::set<std::string>::iterator end);
    videoFetch(const videoFetch& orig);
    virtual ~videoFetch();
    virtual void run();
private:
    std::set<std::string> *vidSet;
    std::set<std::string>::iterator start;
    std::set<std::string>::iterator end;

    std::string* getHtmlContent(const std::string &vid);
    int getVideoUsingCurl(const std::string &url, std::ostream &outStream);
    static size_t curlWriteCallBack( void *ptr, size_t size, size_t nmemb, void *userdata);
    static size_t curlWriteStringCallBack( void *ptr, size_t size, size_t nmemb, void *userdata);
};

#endif	/* VIDEOFETCH_H */

