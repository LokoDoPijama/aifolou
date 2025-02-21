<?php
namespace Aifolou;

enum SendTo {
    case Everyone;
    case EveryoneElse;
    case Me;
    case NoOne;
}